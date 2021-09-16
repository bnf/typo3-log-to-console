<?php
declare(strict_types=1);
namespace Bnf\LogToConsole;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\SingletonInterface;

final class LogHandler extends AbstractWriter
{
    private ?OutputInterface $output = null;

    private $level = LogLevel::DEBUG;

    private array $verbosityLevelMap = [
        OutputInterface::VERBOSITY_QUIET => LogLevel::ERROR,
        OutputInterface::VERBOSITY_NORMAL => LogLevel::WARNING,
        OutputInterface::VERBOSITY_VERBOSE => LogLevel::NOTICE,
        OutputInterface::VERBOSITY_VERY_VERBOSE => LogLevel::INFO,
        OutputInterface::VERBOSITY_DEBUG => LogLevel::DEBUG,
    ];

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $output = $event->getOutput();
        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $this->output = $output;
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        // Disabe the output after a command has been executed.
        $this->output = null;
    }

    public function writeLog(LogRecord $record): self
    {
        if (!$this->isHandling($record)) {
            return $this;
        }

        $data = '';
        $context = $record->getData();
        $message = $record->getMessage();

        if (!empty($context)) {
            // Fold an exception into the message, and string-ify it into context so it can be jsonified.
            if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
                $message .= $this->formatException($context['exception']);
                $context['exception'] = (string)$context['exception'];
            }
            $data = '- ' . json_encode($context);
        }

        $message = sprintf(
            '[%s] component="%s": %s %s',
            strtoupper($record->getLevel()),
            $record->getComponent(),
            $this->interpolate($message, $context),
            $data
        );

        $this->output->write($message, true, $this->output->getVerbosity());

        return $this;
    }

    private function isHandling(LogRecord $record): bool
    {
        if ($this->output === null) {
            return false;
        }

        $this->updateLevel();

        return LogLevel::normalizeLevel($this->level) >= LogLevel::normalizeLevel($record->getLevel());
    }

    private function updateLevel(): void
    {
        $verbosity = $this->output->getVerbosity();
        if (isset($this->verbosityLevelMap[$verbosity])) {
            $this->level = $this->verbosityLevelMap[$verbosity];
        } else {
            $this->level = LogLevel::DEBUG;
        }
    }
}

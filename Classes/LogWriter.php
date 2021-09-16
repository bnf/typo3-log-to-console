<?php
declare(strict_types=1);
namespace Bnf\LogToConsole;

use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class LogWriter extends AbstractWriter
{
    public function writeLog(LogRecord $record): self
    {
        // We cannot inject the LogHandler,
        // as log writers are initialized via constructor arguments,
        GeneralUtility::makeInstance(LogHandler::class)->writeLog($record);
        return $this;
    }
}

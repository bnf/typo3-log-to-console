<?php
defined('TYPO3') || die('Access denied.');

(function() {
    // ugly way to recursively write into all writerConfiguration entries
    $addWriter = function(array &$configuration) use (&$addWriter) {
        foreach ($configuration as $key => $namespace) {
            if ($key === 'writerConfiguration') {
                $configuration[$key][\TYPO3\CMS\Core\Log\LogLevel::DEBUG][\Bnf\LogToConsole\LogWriter::class] = [];
            } elseif (is_array($namespace)) {
                $addWriter($configuration[$key]);
            }
        }
    };

    $addWriter($GLOBALS['TYPO3_CONF_VARS']['LOG']);
})();

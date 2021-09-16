<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Log messages to console in TYPO3 console application',
    'description' => 'Log messages to console in TYPO3 console application',
    'state' => 'beta',
    'author' => 'Benjamin Franzke',
    'author_email' => 'benjaminfranzke@gmail.com',
    'version' => '0.1.0',
    'clearCacheOnLoad' => true,
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.21-11.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Bnf\\LogToConsole\\' => 'Classes/',
        ],
    ],
];

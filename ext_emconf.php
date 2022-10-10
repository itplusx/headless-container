<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Headless Container Content Elements',
    'description' => 'Container Content Elements (EXT:container) json output for EXT:headless',
    'category' => 'fe',
    'author' => 'Ramón Schlosser',
    'author_email' => 'schlosser@itplusx.de',
    'author_company' => 'ITplusX GmbH',
    'state' => 'alpha',
    'version' => '0.8.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.99.99',
            'typo3' => '11.5.0-11.5.99',
            'headless' => '3.0.0-3.99.99',
            'container' => '2.0.0-2.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'ITplusX\\HeadlessContainer\\' => 'Classes/'
        ]
    ],
    'autoload-dev' => [
        'psr-4' => [
            'ITplusX\\HeadlessContainer\\Tests\\' => 'Tests/'
        ]
    ],
];

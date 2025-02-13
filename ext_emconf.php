<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Headless Container Content Elements',
    'description' => 'Container Content Elements (EXT:container) json output for EXT:headless',
    'category' => 'fe',
    'author' => 'Ramón Schlosser',
    'author_email' => 'schlosser@itplusx.de',
    'author_company' => 'ITplusX GmbH',
    'state' => 'stable',
    'version' => '2.1.x-dev',
    'clearCacheOnLoad' => true,
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '12.4.3-13.4.99',
            'headless' => '4.0.0-4.99.99',
            'container' => '2.0.0-3.1.1',
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

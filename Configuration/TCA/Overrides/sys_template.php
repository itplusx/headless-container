<?php
defined('TYPO3') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'headless_container',
    'Configuration/TypoScript',
    'Headless Container Content Elements'
);

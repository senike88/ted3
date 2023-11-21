<?php

    if (!defined('TYPO3_MODE')) {
        die('Access denied.');
    }
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile("ted3", 'Configuration/TypoScript', 'Ted3');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile("ted3", 'Configuration/TypoScript/Content', 'Editable Content Elements');

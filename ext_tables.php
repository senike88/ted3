<?php

    if (!defined('TYPO3')) {
        die('Access denied.');
    }
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile("ted3", 'Configuration/TypoScript', 'Ted3');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile("ted3", 'Configuration/TypoScript/Content', 'Editable Content Elements');

    
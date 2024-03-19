<?php

$newTtContentColumns = array(
    'ted3_renderwidth' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3_renderheight' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3_crop' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $newTtContentColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', array(
    'ted3_settings' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3_hidemobile' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text1' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text2' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text3' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    )
        )
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', array(
    'ted3_settings' => array(
        'config' => array(
            'type' => 'passthrough',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ),
    ),
    'ted3text1' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text2' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text3' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
        )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages_language_overlay', array(
    'ted3text1' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text2' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
    'ted3text3' => array(
        'config' => array(
            'type' => 'passthrough',
        ),
    ),
        )
);

// Add the CType "image"
$languageFilePrefix = 'LLL:EXT:fluid_styled_content/Resources/Private/Language/Database.xlf:';
$frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tt_content', 'CType', [
    'TED³ Gallery',
    'ted3gallery',
    'content-image'
        ], 'header', 'after'
);
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['default'] = 'ted3gallery';
$GLOBALS['TCA']['tt_content']['types']['ted3gallery'] = [
    'showitem' => '
        --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
				assets,header,sectionIndex,
				layout;' . $frontendLanguageFilePrefix . 'layout_formlabel,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.access,
				hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.extended
		'
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tt_content', 'CType', [
    'TED³ Fadegallery',
    'ted3fadegallery',
    'content-image'
        ], 'header', 'after'
);
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['default'] = 'ted3fadegallery';
$GLOBALS['TCA']['tt_content']['types']['ted3fadegallery'] = [
    'showitem' => '
        --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
				assets,header,sectionIndex,
				layout;' . $frontendLanguageFilePrefix . 'layout_formlabel,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.access,
				hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.extended
		'
];


<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    //Override JS
    if (class_exists("\TYPO3\CMS\Core\Core\Environment")) {
        $PATH_site = \TYPO3\CMS\Core\Core\Environment::getPublicPath();
    } else {
        $PATH_site = PATH_site;
    }

    if (!is_dir($PATH_site . "/fileadmin/ted3")) {

        @mkdir($PATH_site . "/fileadmin/ted3");
        @mkdir($PATH_site . "/fileadmin/ted3/js");
    }

    if (!file_exists($PATH_site . "/fileadmin/ted3/js/ted3-user-override.js")) {
        //  echo "notex"; exit;
        @copy($PATH_site . "/typo3conf/ext/ted3/Resources/Public/Dummy/ted3-user-override.js", $PATH_site . "/fileadmin/ted3/js/ted3-user-override.js");
    }
}


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'DS.Ted3', 'Fe', array(
    \DS\Ted3\Controller\CrudController::class => 'tce,movecontent,delete,copy,createcontent,hide,translate,sort,addflexelement,copycontent,settings,test',
    \DS\Ted3\Controller\BackendController::class => 'module,route,link',
//    \DS\Ted3\Controller\NewContentElementController::class => 'wizard'
        ),
        // non-cacheable actions
        array(
            \DS\Ted3\Controller\CrudController::class => 'tce,movecontent,delete,copy,createcontent,hide,translate,sort,addflexelement,copycontent,settings,test',
            \DS\Ted3\Controller\BackendController::class => 'module,route,link',
//    \DS\Ted3\Controller\NewContentElementController::class => 'wizard'
        )
);
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .= ',ted3text1,ted3text2,ted3text3';
$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] = $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] . ",webp";


if (in_array($_GET['type'], array(4455, 4456, 4457, 4500, 777, 4457))) {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'] = 0;
}


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][] = \DS\Ted3\Hooks\FrontendRenderer::class . '->tsfe';
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['getPageOverlay'][] = \DS\Ted3\Hooks\PageRepositoryGetPageOverlay::class;


if (TYPO3_MODE === 'BE') {
    call_user_func(
            function ($extKey) {
                // Get the extension configuration
                $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);

                if (!isset($extConf['loadted3ContentElementWizardTsConfig']) || (int) $extConf['loadted3ContentElementWizardTsConfig'] === 1) {
                    // Include new content elements to modWizards
                    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ted3/Configuration/TypoScript/pageTS.ts">');
                }

                $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
                $dispatcher->connect(
                        \TYPO3\CMS\Extensionmanager\Controller\ConfigurationController::class, 'afterExtensionConfigurationWrite', \TYPO3\CMS\FluidStyledContent\Hooks\TcaCacheClearing::class, 'clearTcaCache'
                );
            }, "Ted3"
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ted3/Configuration/TypoScript/userTS.ts">');

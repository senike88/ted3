<?php

namespace DS\Ted3\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendRenderer {

    public function tsfe($params, $adsf) {
        

        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']); exit;
        if ($GLOBALS['TSFE']->beUserLogin) {
            //...
        } else { // FIX ab T3 10
            if (is_object($GLOBALS['BE_USER'])) {
                $GLOBALS['TSFE']->beUserLogin = $GLOBALS['BE_USER']->backendCheckLogin();
            }
        }


        if ($GLOBALS['TSFE']->beUserLogin && $GLOBALS['BE_USER']->doesUserHaveAccess($GLOBALS['TSFE']->page ,2) ) {
            
            if(!$GLOBALS['TSFE']->pSetup){
                //$GLOBALS['TSFE']->pageCache->flush();
                $CacheService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\CacheService');
                $CacheService->clearPageCache(array($GLOBALS['TSFE']->id));
                
                $this->refreshOnce();
            }
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']->pSetup['ted3']); exit;
            if (@$GLOBALS['TSFE']->pSetup['ted3'] == "1") {

//                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($sites['mysite']->getConfiguration()['test']);
//                exit;
                //   if ($GLOBALS['TSFE']->pSetup['typeNum'] != '4500' && $GLOBALS['TSFE']->pSetup['typeNum'] != '1533906435' &&  != 'preventTed3') {
                $GLOBALS['TSFE']->pSetup['1'] = "FLUIDTEMPLATE";
                $GLOBALS['TSFE']->pSetup['1.']['file'] = 'EXT:ted3/Resources/Private/Main.html';
                // }
                // echo "test"; exit;
                // SHOW-HIDDEN TILL TYPO3-9
                $GLOBALS['TSFE']->showHiddenRecords = true;
                //$GLOBALS['TSFE']->fe_user->showHiddenRecords  = true;
                //$GLOBALS['BE_USER']->showHiddenRecords = true;
                // SHOW-HIDDEN FROM TYPO3-10
                $context = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
                $aspect = $context->getAspect('visibility');
                $newAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\VisibilityAspect::class, $aspect->includeHiddenPages(), true, $aspect->includeDeletedRecords());
                $context->setAspect('visibility', $newAspect);

                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($newAspect);
                // exit;
                
                // Show elements out of publish-range
                unset($GLOBALS['TCA']['tt_content']['ctrl']['enablecolumns']['starttime']);
                unset($GLOBALS['TCA']['tt_content']['ctrl']['enablecolumns']['endtime']);
//             \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $GLOBALS['TSFE']); exit;  


                $GLOBALS['TSFE']->no_cache = TRUE;
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $GLOBALS['TSFE']); exit;  
                $tmpJS = $GLOBALS['TSFE']->pSetup['includeJS.'];
                if (!is_array($tmpJS)) {
                    $tmpJS = array();
                }
                $GLOBALS['TSFE']->pSetup['includeJS.'] = array_merge(array(
                    'ted3Jquery' => 'EXT:ted3/Resources/Public/js/jquery-1.11.3.min.js',
                    'ted3JqueryUi' => 'EXT:ted3/Resources/Public/js/jquery-ui.min.js',
                    'tinymce' => 'EXT:ted3/Resources/Public/tinymce/tinymce.min.js',
                    'tinymceJQ' => 'EXT:ted3/Resources/Public/tinymce/jquery.tinymce.min.js',
                    'ted3Api' => 'EXT:ted3/Resources/Public/js/api.js',
                    'ted3add' => 'EXT:ted3/Resources/Public/js/addzone.js',
                    'tedText' => 'EXT:ted3/Resources/Public/js/ui-text.js',
                    'ted3element' => 'EXT:ted3/Resources/Public/js/element.js',
                    'ted3container' => 'EXT:ted3/Resources/Public/js/container.js',
                    'ted3root' => 'EXT:ted3/Resources/Public/js/ui-ted3root.js',
                    'ted3init' => 'EXT:ted3/Resources/Public/js/init.js',
                    'ted3useroverride' => 'fileadmin/ted3/js/ted3-user-override.js'
                        ), $tmpJS);
                unset($tmpJS);

                $tmpCSS = $GLOBALS['TSFE']->pSetup['includeCSS.'];
                if (!is_array($tmpCSS)) {
                    $tmpCSS = array();
                }
                $GLOBALS['TSFE']->pSetup['includeCSS.'] = array_merge(array(
                    'tedFontAwesome' => ' EXT:ted3/Resources/Public/fonticons/css/font-awesome.min.css',
                    'tedJqueryUi' => 'EXT:ted3/Resources/Public/css/jquery-ui.css',
                    'tedGeneral' => 'EXT:ted3/Resources/Public/css/general.css',
                    'tedContent' => ' EXT:ted3/Resources/Public/css/content.css',
                    'tedProps' => 'EXT:ted3/Resources/Public/css/properties.css'
                        ), $tmpCSS);
                unset($tmpCSS);

                // BAR
            }
        }
        if ($GLOBALS['TSFE']->pSetup['ted3'] == 1) {
             $GLOBALS['TSFE']->pageRenderer->addCssFile("EXT:ted3/Resources/Public/css/ted3-frontend.css");
        }
    }
    
    private function refreshOnce() {
        //echo "asdf"; exit;
        if (!isset($_COOKIE['rcounter'])) {
            setcookie('rcounter', 0);
        }

        if (isset($_COOKIE['rcounter']) && $_COOKIE['rcounter'] < 2) {
            $current_val = $_COOKIE['rcounter'];
            $current_val++;
            setcookie('rcounter', $current_val);
            header('refresh: 0');
            echo "Force refresh ... ".$_COOKIE['rcounter'];
            exit;
        } else {
            //echo 'no more reloads';
            setcookie('rcounter', 0);
        }
    }

}

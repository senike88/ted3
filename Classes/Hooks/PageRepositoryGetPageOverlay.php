<?php

namespace DS\Ted3\Hooks;

class PageRepositoryGetPageOverlay implements \TYPO3\CMS\Core\Domain\Repository\PageRepositoryGetPageOverlayHookInterface {

    /**
     * enables to preprocess the pageoverlay
     *
     * @param array $pageInput The page record
     * @param int $lUid The overlay language
     * @param \TYPO3\CMS\Frontend\Page\PageRepository $parent The calling parent object
     * @return void
     */
    public function getPageOverlay_preProcess(&$pageInput, &$lUid, \TYPO3\CMS\Core\Domain\Repository\PageRepository $parent) {
   
        if ($GLOBALS['TSFE']->beUserLogin) {
            //...
        } else { // FIX t3-10
            if (is_object($GLOBALS['BE_USER'])) {
                
                if( !is_object($GLOBALS['TSFE']) ){
                    $GLOBALS['TSFE'] = new \stdClass();
                }
                $GLOBALS['TSFE']->beUserLogin = $GLOBALS['BE_USER']->backendCheckLogin();
            }
        }
        
        if ($GLOBALS['TSFE']->beUserLogin) {
             //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pageInput['doktype']); exit;
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pageInput); exit;
	    if($pageInput['doktype'] == 4){
		$pageInput['doktype'] = 1;
		$pageInput['_originalDoktype'] = 4;
	    }
	}
   
    }

}

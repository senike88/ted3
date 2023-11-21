<?php

namespace DS\Ted3\Controller;

use Psr\Http\Message\ResponseInterface;

//use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Extended controller for link browser
 * @internal This is a specific Backend Controller implementation and is not considered part of the Public TYPO3 API.
 */
class CrudController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    public function initializeAction() {
        if(isset($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->backendCheckLogin() ){
           // passtrough 
        }else{
            echo "Ted3Controller: Access denied.";
            exit;
        }

        if (!isset($GLOBALS['LANG'])) {
            $GLOBALS['LANG'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Lang\LanguageService');
            $GLOBALS['LANG']->init("de");
            $GLOBALS['LANG']->csConvObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Charset\CharsetConverter');
        } else if (!isset($GLOBALS['LANG']->csConvObj) || $GLOBALS['LANG']->csConvObj == null) {
            $GLOBALS['LANG']->init("de");
            $GLOBALS['LANG']->csConvObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Charset\CharsetConverter');
        }
        $this->tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
    }

    public function sendAjaxResponse($data) {

        echo json_encode($data);
        exit;
    }

    /**
     * @param integer $uid
     * @param integer $pid
     * @param integer $colpos
     * @param integer $container
     * @param integer $beforeUid
     * @param array $fields
     */
    public function movecontentAction($uid, $pid = 0, $colpos = -2, $container = 0, $beforeUid = 0, $fields = array()) {


        $table = 'tt_content';
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array($uid,$table,$pid,$colpos,$container,$beforeUid));
        //exit;
        $cmd = array();
        $data = array();
        $data[$table][$uid][''] = $pid;
        if ($beforeUid) {

            $cmd[$table][$uid]['move'] = '-' . $beforeUid;
        } else {
            $cmd[$table][$uid]['move'] = $pid;
        }
        if ($colpos > -2) {
            $data[$table][$uid]['colPos'] = $colpos;
        }

        if ($container) {
            $data[$table][$uid]['colPos'] = -1;
        }
        $data[$table][$uid]['tx_gridelements_container'] = $container;
        $data[$table][$uid]['tx_gridelements_columns'] = $colpos;

        //newWidthReplace for images
        foreach ($fields as $field => $pictures) {
            foreach ($pictures as $uid => $newWidth) {
                $data['sys_file_reference'][$uid] = array(
                    'ted3_renderwidth' => (int) $newWidth
                );
            }
        }

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $this->tce->process_datamap();
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->tce);
        // exit;
        $this->sendAjaxResponse(array('success' => true));
    }

    public function testAction() {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['BE_USER']->userTS['options.']['ted3.']['dontHideAtNewContent']);
        exit;
    }

    /**
     * @param integer $uid
     * @param integer $pid
     * @param integer $colpos
     * @param integer $container
     * @param integer $beforeUid
     * @param array $fields
     */
    public function copycontentAction($uid, $pid = 0, $colpos = -2, $container = 0, $beforeUid = 0, $fields = array()) {
        $table = 'tt_content';
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array($uid,$table,$pid,$colpos,$container,$beforeUid));
        //exit;

        $cmd = array();
        $data = array();
        //$data[$table][$uid][''] = $pid;
        if ($beforeUid) {

            $cmd[$table][$uid]['copy'] = '-' . $beforeUid;
        } else {
            $cmd[$table][$uid]['copy'] = $pid;
        }

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $cmd = array();
        $newId = $this->tce->copyMappingArray_merged[$table][$uid];
        //echo $newId; exit;
//        $data[$table][$newId]['hidden'] = 0;
        if ($GLOBALS['BE_USER']->getTSConfig()['options.']['ted3.']['dontHideAtNewContent']) {
            $data[$table][$newId]['hidden'] = 0;
        }

        if ($colpos > -2) {
            $data[$table][$newId]['colPos'] = $colpos;
        }

        if ($container) {
            $data[$table][$newId]['colPos'] = -1;
        }
        $data[$table][$newId]['tx_gridelements_container'] = $container;
        $data[$table][$newId]['tx_gridelements_columns'] = $colpos;

        //newWidthReplace for images
        foreach ($fields as $field => $pictures) {
            foreach ($pictures as $uid => $newWidth) {
                $data['sys_file_reference'][$uid] = array(
                    'ted3_renderwidth' => (int) $newWidth
                );
            }
        }

        $this->tce->start($data, $cmd);
        //$this->tce->process_cmdmap();
        $this->tce->process_datamap();
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->tce);
        // exit;
        if (class_exists("ArminVieweg\Dce\Cache")) {
            \ArminVieweg\Dce\Cache::clear();
        }
        $this->sendAjaxResponse(array('success' => true, 'newuid' => $newId));
    }

    /**
     * @param string $identifier
     * @param integer $pid
     * @param integer $colpos
     * @param integer $container
     * @param integer $beforeUid
     * @param integer $currentLangId
     * @param array $fields
     * @param array $tt_content
     */
    public function createcontentAction($identifier = "", $pid = 0, $colpos = -2, $container = 0, $beforeUid = 0,$currentLangId=0, $fields = array(), $tt_content = array()) {
        //  echo "createcontent".$identifier; exit;

        if($identifier == "notDefined"){
            $this->sendAjaxResponse(array('success' => false, 'newuid' => null));
        }
        
        $table = "tt_content";
        $cmd = array();
        $data = array();
        $data[$table]['NEW9823be8'] = array(
            'pid' => $pid, //$GLOBALS['TSFE']->page['uid']
            'hidden' => 1,
            'CType' => $identifier,
            'sys_language_uid' => $currentLangId
        );

        //    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['BE_USER']); exit;

        if (@$GLOBALS['BE_USER']->getTSConfig()['options.']['ted3.']['dontHideAtNewContent']) {
            $data[$table]['NEW9823be8']['hidden'] = 0;
        }
        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($data); exit;
        //prefill fields
        foreach ($fields as $field => $fData) {
            if ($fData['type'] == "file") {
                $data['sys_file_reference']['NEW1234'] = array(
                    'table_local' => 'sys_file',
                    'uid_local' => $fData['data'],
                    'tablenames' => $table,
                    'uid_foreign' => 'NEW9823be87', // uid of your content record
                    'fieldname' => $field,
                    'pid' => $pid, // page id of content record
                    'ted3_renderwidth' => $fData['ted3_renderwidth'],
                );
                $data[$table]['NEW9823be8'][$field] = 'NEW1234';
            }
        }
        foreach ($tt_content as $key => $val) {
            $data[$table]['NEW9823be8'][$key] = $val;
        }
        $this->tce->start($data, array());
        $this->tce->process_datamap();
        $data = array();

        $uid = $this->tce->substNEWwithIDs['NEW9823be8'];
        if ($beforeUid) {
            $cmd[$table][$uid]['move'] = '-' . $beforeUid;
        } else {
            $cmd[$table][$uid]['move'] = $pid;
        }
        if ($colpos > -2) {
            $data[$table][$uid]['colPos'] = $colpos;
        }

        if ($container) {
            $data[$table][$uid]['colPos'] = -1;
            $data[$table][$uid]['tx_gridelements_container'] = $container;
            $data[$table][$uid]['tx_gridelements_columns'] = $colpos;
        }

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $this->tce->process_datamap();


        $CacheService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\CacheService');
        $CacheService->clearPageCache(array($pid));


        $this->sendAjaxResponse(array('success' => true, 'newuid' => $uid));
    }

    /**
     * @param array $data
     * @param array $cmd
     * @param integer $pid
     */
    public function tceAction($data = array(), $cmd = array(), $pid = 0) {
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($data); exit;
        //$content = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', $table, 'uid=' . $uid);
        //echo "mc"; exit;

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $this->tce->process_datamap();

        $newuid = @$this->tce->substNEWwithIDs['NEWRECORD'];
        if (@$cmd) {
            $table = key($cmd);
            $uid = key($cmd[$table]);
            $copyuid = @$this->tce->copyMappingArray_merged[$table][$uid];
        }

        if ($pid > 0) {
            $CacheService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\CacheService');
            $CacheService->clearPageCache(array($pid));
        }

        $this->sendAjaxResponse(array('success' => true, 'newuid' => $newuid, 'copyuid' => @$copyuid));
    }

    /**
     * @param integer $uid
     * @param integer $uid
     * @param string $direction
     */
    public function sortfalrefAction($uid, $direction) {
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']); exit;
        //$content = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', $table, 'uid=' . $uid);

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $this->tce->process_datamap();

        $newuid = $this->tce->substNEWwithIDs['NEWRECORD'];
        if ($cmd) {
            $table = key($cmd);
            $uid = key($cmd[$table]);
            $copyuid = $this->tce->copyMappingArray_merged[$table][$uid];
        }


        $this->sendAjaxResponse(array('success' => true, 'newuid' => $newuid, 'copyuid' => $copyuid));
    }

    /**
     * @param integer $uid
     * @param string $table
     */
    public function deleteAction($uid, $table = "tt_content") {

        $cmd = array();
        $cmd[$table][$uid]['delete'] = 1;

        $this->tce->start(array(), $cmd);
        $this->tce->process_cmdmap();

        $this->sendAjaxResponse(array('success' => true));
    }

    /**
     * @param integer $uid
     * @param string $table
     * @param array $settings
     */
    public function settingsAction($uid, $table = "tt_content", $settings = array()) {
        $data = array();
        $data[$table][$uid]['ted3_settings'] = json_encode($settings);

        $this->tce->start($data, array());
        $this->tce->process_datamap();

        $this->sendAjaxResponse(array('success' => true));
    }

    /**
     * @param integer $uid
     * @param integer $lang
     * @param string $table
     */
    public function translateAction($uid, $lang, $table = "tt_content") {

     //   $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Routing\\UriBuilder');
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->uriBuilder->reset()->setTargetPageUid(1)->build());
    //  $translatedPageUrl = $this->uriBuilder->reset()->setTargetPageUid(1)->setLanguage($lang)->build();
   //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->uriBuilder->setTargetPageUid(1)->uriFor('')->build()); exit;
        $translatedPageUrl = "";

        $data = array();
        $cmd = array();
        $cmd[$table][$uid]['localize'] = $lang;

        $this->tce->start($data, $cmd);
        $this->tce->process_cmdmap();
        $transuid = $this->tce->copyMappingArray_merged[$table][$uid];
        if ($table == "pages" && !$transuid) {

            // check if translated and hidden
            $translation = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'pages_language_overlay', 'pid = ' . $uid . ' and hidden=1 and deleted=0 and sys_language_uid=' . $lang);

            if (isset($translation[0])) {
                $id = $translation[0]['uid'];
                $data['pages_language_overlay'][$id]['hidden'] = 0;
                $transuid = $id;
            }
        } else {
            $data[$table][$transuid]['hidden'] = 0;
        }
        $this->tce->start($data, array());
        $this->tce->process_datamap();

        if ($table == "pages") {
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uid); exit;
            $translatedPageUrl = $this->uriBuilder->reset()->setTargetPageUid($transuid)->build();
        }
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->tce); exit;
        $this->sendAjaxResponse(array('success' => true, 'transuid' => $transuid, 'translatedPageUrl' => $translatedPageUrl));
    }

}

?>
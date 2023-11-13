<?php

namespace DS\Ted3\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
    
    public function initializeAction() {
         if(isset($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->backendCheckLogin() ){
           // passtrough 
        }else{
            echo "Ted3Controller: Access denied.";
            exit;
        }
    }
    
    public function uploadAction() {

        $extBlackList = $this->settings['extensionBlackList'];
        $extWhiteList = $this->settings['extensionWhiteList'];

        foreach ($_FILES as $file) {
            $explodetFilename = explode(".", $file['name'][0]);
            // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump();
            $fileExtension = strtolower(end($explodetFilename));
            //echo $fileExtension; exit;
            if (in_array($fileExtension, $extBlackList)) {
                echo json_encode(array('success' => false, 'message' => 'Filetype ' . $fileExtension . ' not allowed (type in blacklist)'));
                exit;
            }
            if ($extWhiteList) {
                if (!in_array($fileExtension, $extWhiteList)) {
                    echo json_encode(array('success' => false, 'message' => 'Filetype ' . $fileExtension . ' not allowed (type not in whitelist)'));
                    exit;
                }
            }
        }

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($_FILES);
        $this->fileProcessor = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Utility\\File\\ExtendedFileUtility');

//         $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']
        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);

        $this->fileProcessor->start(array());

        $this->fileProcessor->setActionPermissions();
        $this->fileProcessor->setExistingFilesConflictMode(\TYPO3\CMS\Core\Resource\DuplicationBehavior::RENAME);

        $this->fileProcessor->start(array(
            'upload' => array(
                array(
                    'data' => 1,
                    'target' => 'fileadmin/user_upload/'
                )
            )
        ));
        $data = array('success' => true);
        $upload = $this->fileProcessor->processData();
        foreach ($_FILES as $fileupload) {
            if (isset($fileupload['error'][0]) && $fileupload['error'][0] > 0) {
                $data = array('success' => false, 'message' => 'Error on fileupload: PHP-Error-Number: ' . $fileupload['error'][0]);
            }
        }
        if ($em = $this->fileProcessor->getErrorMessages()) {
            $data = array('success' => false, 'message' => 'Error on fileupload: ' . $em[0]);
        }

        foreach ($upload['upload'][0] as $file) {
            $data['files'][] = $file->getUid();
        }
        echo json_encode($data);
        exit;
    }

}

?>
<?php

namespace DS\Ted3\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FilepoolController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    public function initializeAction() {
        if (isset($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->backendCheckLogin()) {
            // passtrough 
        } else {
            echo "Ted3Controller: Access denied.";
            exit;
        }
    }

    public function indexAction() {

        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->settings); exit;
        $blackListFileadmin = $this->settings['blackListFileadmin'];

        $this->fileStorage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
        $storages = $this->fileStorage->findAll();
        //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($storages); exit;

        $rootFolder = $storages[0]->getRootLevelFolder();

        $storagefolders = $rootFolder->getSubfolders();

        foreach ($storagefolders as $fileadminFolder) {
            //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($fileadminFolder); exit;
            if (!in_array($fileadminFolder->getName(), $blackListFileadmin)) {
                $useableStoragefolders[] = $fileadminFolder;
            }
        }

        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($storagefolders); exit;
        $this->view->assign('storagefolders', $useableStoragefolders);
    }

    /**
     * @param int $file
     */
    public function deletefileAction($file) {
        $this->fileRep = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
        $f = $this->fileRep->findByUid($file);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(get_class_methods($f->getStorage()));
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump();
        //exit;
        $fileReferences = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_file_reference', 'uid_local = ' . $file . ' and deleted=0');

        if (count($fileReferences) > 0) {
            $result = array(
                'deletesuccess' => 0,
                'references' => count($fileReferences)
            );
        } else {
            $f->getStorage()->deleteFile($f);
            $result = array(
                'deletesuccess' => 1
            );
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    /**
     * @param string $folder
     * @param int $limit
     * @param int $offset
     */
    public function listAction($folder = "/user_upload/", $limit = -1, $offset = 0) {


        $this->fileStorage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
        $storages = $this->fileStorage->findAll();

        $rootFolder = $storages[0]->getRootLevelFolder();

        // $storagefolders = $rootFolder->getSubfolders();
        $currentFolder = $rootFolder->getSubfolder($folder);
        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump();
        //  $files = $currentFolder->getFiles();
        //Umlaut-Fix
        try {
            $files = $currentFolder->getFiles();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(array("umlauterror"));
            exit;
        }

        $objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $imageService = $objectManager->get(\TYPO3\CMS\Extbase\Service\ImageService::class);
        $fileIconPath = "typo3conf/ext/ted3/Resources/Public/Icons/Newfiles/";

        $i = 0;
        $filecount = 0;
        foreach ($files as $file) {

//            
            //   $dimension = '-';
            $fe = $file->getExtension();
            if (in_array($fe, explode(",", $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']))) {
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($file);
                //$image = $imageService->getImage("fileadmin/" . $file->getIdentifier(), null, false);
                $processingInstructions = array(
//                'width' => '250',
                    'height' => '165',
                );
                $processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
                $imageUri = $imageService->getImageUri($processedImage);

                $imagedim = getimagesize("fileadmin/" . $file->getIdentifier());
                $dimension = $imagedim[0] . " x " . $imagedim[1];
                $isimage = 1;
            } else {
                $imageUri = $fileIconPath . "default.png";
                $isimage = 0;
                $dimension = "";
            }
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($file);
//            exit;
            $jsonData[] = array(
                'uid' => $file->getUid(),
                'name' => $file->getName(),
                'identifier' => $file->getIdentifier(),
                'thumbnail' => $imageUri,
                'cdate' => $file->getCreationTime(),
                'size' => round($file->getSize() / 1000, 2),
                'dimension' => $dimension,
                'isimage' => $isimage,
                'ext' => $file->getExtension()
            );
            $filecount++;
            $i++;
        }

        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($jsonData);
        ///  exit;
        header('Content-Type: application/json');
        //http_response_code(201);
        echo json_encode($jsonData);
        exit;
    }

    /**
     * @param string $target
     */
    public function uploadAction($target = "/user_upload/") {

        $extBlackList = $this->settings['extensionBlackList'];
        $extWhiteList = $this->settings['extensionWhiteList'];
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($extBlackList); exit;

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


        $target = "fileadmin" . $target;
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
                    'target' => $target
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

        //    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($upload['upload']); exit;
        ///  exit;

        $objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $imageService = $objectManager->get(\TYPO3\CMS\Extbase\Service\ImageService::class);
        $fileIconPath = "typo3conf/ext/ted3/Resources/Public/Icons/Files/";
        $fileIconPath = "typo3conf/ext/ted3/Resources/Public/Icons/Newfiles/";

        foreach ($upload['upload'][0] as $file) {

            $fe = $file->getExtension();
            if (in_array($fe, explode(",", $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']))) {
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($file);
                //$image = $imageService->getImage("fileadmin/" . $file->getIdentifier(), null, false);
                $processingInstructions = array(
//                'width' => '250',
                    'height' => '165',
                );
                $processedImage = $imageService->applyProcessingInstructions($file, $processingInstructions);
                $imageUri = $imageService->getImageUri($processedImage);

                $imagedim = getimagesize("fileadmin/" . $file->getIdentifier());
                $dimension = $imagedim[0] . " x " . $imagedim[1];
                $isimage = 1;
            } else {
                $imageUri = $fileIconPath . "default.png";
                $isimage = 0;
                $dimension = "";
            }


            $jsonData[] = array(
                'uid' => $file->getUid(),
                'name' => $file->getName(),
                'identifier' => $file->getIdentifier(),
                'thumbnail' => $imageUri,
                'cdate' => $file->getCreationTime(),
                'size' => round($file->getSize() / 1000, 2),
                'dimension' => $dimension,
                'isimage' => $isimage,
                'ext' => $file->getExtension(),
                'isnew' => 1,
            );
        }
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($jsonData); exit;
        //header('Content-Type: application/json');
        echo json_encode($jsonData);
        exit;
    }
}

?>
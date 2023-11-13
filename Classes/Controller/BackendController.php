<?php

namespace DS\Ted3\Controller;

class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @param string $moduleName
     */
    public function moduleAction($moduleName) {
        $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("params");
        $urlParams = $params['urlParameters'];
        if (!$urlParams) {
            $urlParams = array();
        }
        $url = \TYPO3\CMS\Backend\Utility\BackendUtility::getModuleUrl($moduleName, $urlParams);

        $this->redirectToUri("typo3/" . $url, 0, 301);
    }


    public function routeAction() {

        $route = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("route");
        $mode = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("mode");
        $returnUrl = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("returnUrl");
        $table = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("table");
        $uid = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP("uid");
        $uB = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Routing\UriBuilder');



        if ("wizard_element_browser" == $route && $mode) {
            $uri = $uB->buildUriFromRoute($route, array(
                'mode' => $mode,
                'returnUrl' => $returnUrl
            ));
        } else if ("record_edit" == $route) {
            $uri = $uB->buildUriFromRoute($route, array(
                'edit' => array(
                    $table => array(
                        $uid => "edit"
                    )
                ),
                'returnUrl' => $returnUrl
            ));
            //  echo $uri; exit;
        } else if ("ted3_new_content_element_wizard" == $route) {
            // echo $uid; exit;
            // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump("sdf".$GLOBALS['TSFE']->id); exit;   
            $uri = $uB->buildUriFromRoute("ted3_new_content_element_wizard", array(
                'id' => $uid,
                'uid_pid' => 1,
                'sys_language_uid' => 0,
                'colPos' => '0',
                'returnUrl' => $returnUrl
            ));
            //    echo $uri; exit;
        }

        else {

            $urlParameters = [
                'P' => [
                    'table' => "",
                    'uid' => 1,
                    'fieldName' => "header_link",
                    'recordType' => "",
                    'pid' => 0,
                    'richtextConfigurationName' => "",
                ],
            ];


            $uri = $uB->buildUriFromRoute('wizard_link');
            echo $uri;
            exit;
        }
        //     echo $uri; exit;
        $this->redirectToUri($uri, 0, 301);
    }

    public function contentelementAction() {

    }
    
    


    /**
     * @param string $typolink
     * @param int $lang
     */
    public function linkAction($typolink,$lang=0) {


        if (count(explode("#", $typolink)) == 2) {
            $ankArray = explode("#", $typolink);
            $typolink = $ankArray[0];
            $ankArray[1] = str_replace("/", "", $ankArray[1]);
        }
        
        $uri = $this->configurationManager->getContentObject()->typoLink_URL(array('parameter' => $typolink,'language'=>$lang));

        //Anker
        if ($ankArray[1]) {
            $uri = $uri . "#" . $ankArray[1];
            $uri = str_replace("//", "/", $uri);
        }

        echo $uri;
        exit;
    }


}

?>

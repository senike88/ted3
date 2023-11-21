<?php

namespace DS\Ted3\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

class RouteuriViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    /**
     * Initialize arguments
     */
    public function initializeArguments() {
        $this->registerArgument('route', 'string', '');
        $this->registerArgument('options', 'string', '', false, array());
        $this->registerArgument('mode', 'string', '', false, '');
    }

    public function render() {

//  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($renderingContext); exit;
        $route = $this->arguments['route'];
        $options = $this->arguments['options'];
        $mode = $this->arguments['mode'];
        
           $uriB = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Routing\UriBuilder');
        //  echo $uriB; exit;
        // echo $uriB->buildUriFromAjaxId($route, $options); 
        //var_dump($_SERVER); exit;
        //  echo $_SERVER['SCRIPT_URI'].$uriB->buildUriFromRoute($route, $options); exit;
        //  
        //$_SERVER['SCRIPT_URI'] -> Fix Login-Link when baseUrl not yet correct

        $domain = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
        if ($mode == "ajaxid") {
            return $domain . $uriB->buildUriFromAjaxId($route, $options);
        } else {
            return $domain . $uriB->buildUriFromRoute($route, $options);
        }
    }


}

?>

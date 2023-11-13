<?php

namespace DS\Ted3\ViewHelpers;

class IsImageViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments() {

        $this->registerArgument('ext', 'string', '', false, "");
    }

    public function render() {

        $ext = $this->arguments['ext'];
        if (in_array($ext, explode(",", $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']))) {
            return true;
        }
        
        return false;
    }

}

?>

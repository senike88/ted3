<?php

namespace DS\Ted3\ViewHelpers;

class IsExternalVideoViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments() {

        $this->registerArgument('ext', 'string', '', false, "");
    }

    public function render() {

        $ext = $this->arguments['ext'];
        if (in_array($ext, array("youtube","vimeo"))) {
            return true;
        }
        
        return false;
    }

}

?>

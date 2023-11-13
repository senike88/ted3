<?php

namespace DS\Ted3\ViewHelpers;

class ValFromJsonViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments() {

        $this->registerArgument('json', 'mixed', '', false, null);
        $this->registerArgument('key', 'string', '', false, "");
    }

    public function render() {

        $json = $this->arguments['json'];
        $key = $this->arguments['key'];

        $array = json_decode($json, true);
        return $array[$key];
    }

}

?>

<?php

namespace DS\Ted3\ViewHelpers;

class ClasslistViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper{

    public function initializeArguments() {

        $this->registerArgument('classes', 'mixed', '');
        $this->registerArgument('delimiter', 'string', '', false, ",");
    }

    public function render() {

        $classes = $this->arguments['classes'];
//        var_dump($classes);
        $delimiter = $this->arguments['delimiter'];

        if (!is_array($classes)) {
            $classes = explode($delimiter, $classes);
        }
        foreach ($classes as $class) {
            $list .= $class . " ";
        }
        return $list;
    }

}

?>

<?php

namespace DS\Ted3\ViewHelpers;

class NgvarViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     *
     * 
     * @param string $var
     */
    public function render($var) {
        return $var;
	return '{{'.$var.'}}';
    }
    
}

?>

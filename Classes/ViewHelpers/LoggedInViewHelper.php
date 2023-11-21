<?php

namespace DS\Ted3\ViewHelpers;

class LoggedInViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {


    public function render() {



       return ($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? 1 : 0;


    }

  

}

?>

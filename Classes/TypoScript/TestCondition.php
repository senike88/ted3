<?php

namespace DS\Ted3\TypoScript;


/**
 * Example condition
 */
class TestCondition extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition {

    /**
     * Evaluate condition
     */
    public function matchCondition(array $conditionParameters) {
        echo "drin"; exit;
        return true;
       // echo "111110func";
//      //  echo "111110func"; exit;
//      var_dump($GLOBALS['BE_USER']); exit;
//          return true;
//        if ($GLOBALS['TSFE']->beUserLogin) {
//            echo "8true";
//            return true;
//        } else if (is_object($GLOBALS['BE_USER'])) {
//            echo "10true";
//            return $GLOBALS['BE_USER']->backendCheckLogin();
//        }
//        return false;
    }

}

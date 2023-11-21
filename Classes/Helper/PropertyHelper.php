<?php

namespace DS\Ted3\Helper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PropertyHelper {

    public static function getProperty($obj, $key) {
        if (is_array($obj)) {
            return $obj[$key];
        } else if (is_object($obj)) {
            $getter = "get" . ucfirst($key);
            if (method_exists($obj, $getter)) {
                return $obj->$getter();
            }
           // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($obj); exit;
            return "property ".$key." not found in this record";
        }
    }

}

?>
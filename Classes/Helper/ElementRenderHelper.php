<?php
namespace DS\Ted3\Helper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ElementRenderHelper
 *
 * @author dominik
 */
class ElementRenderHelper {
    //put your code here
    
    public static function render($table,$id,$cObj){
        $config = array(
            'tables' => $table,
            'source' => $id,
            'dontCheckPid' => 1
        );
        
        return $cObj->RECORDS($config);
    }
}

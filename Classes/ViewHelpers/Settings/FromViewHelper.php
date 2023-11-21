<?php

namespace DS\Ted3\ViewHelpers\Settings;


class FormViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

      /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;
    
    /**
     */
    public function render() {
        if(($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE){
            
           
            $saveLabel = "Save";
            
            
             return '<form method="post" style="display:none;" action="" class="ted3-element-settingsform"><table>'.$this->renderChildren().'</table><input type="submit" class="ted3-element-settingsform-sm" value="'.$saveLabel.'" /></form>';
        }else{
            return null;
        }
       
    }

}

?>
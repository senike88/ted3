<?php

namespace DS\Ted3\ViewHelpers\Settings;

use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;

class SelectViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    
    protected $escapeOutput = false;
        

    public function initializeArguments() {

        $this->registerArgument('name', 'string', '');
        $this->registerArgument('label', 'string', '',false,'');
        $this->registerArgument('multiple', 'integer', '',false,0);
        $this->registerArgument('required', 'string', '',false,'');
        $this->registerArgument('size', 'string', '',false,'');
        $this->registerArgument('options', 'array', '',false,array());
    }
    
 
    public function render() {

        $name = $this->arguments['name'];
        $label = $this->arguments['label'];
        $multiple = $this->arguments['multiple'];
        $required = $this->arguments['required'];
        $size = $this->arguments['size'];
        $options = $this->arguments['options'];
        
       // var_dump($options); exit;
        
	if (!($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE) {
	    return null;
	}

	if ($this->viewHelperVariableContainer->exists(AbstractElementViewHelper::class, 'record')) {
	    $record = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'record');
	    if (is_array($record) && isset($record['ted3_settings'])) {
		$ted3settings = json_decode($record['ted3_settings'], true);
	    }
	}
	$valArray = array();
	if (!$value) {
	    $value = $ted3settings[$name];
	    if (count(explode(",", $value)) > 1) {
		$valArray = explode(",", $value);
	    } 
//	    else {
//		if (!$value) {
//		    $value = $default;
//		}
//	    }
	}
	if ($required) {
	    $requiredAttr = 'required="1"';
	}
	if($multiple){
	    $multipleAttr = 'multiple="1"';
	}

	foreach ($options as $key => $lab) {
	    if ($value !== null && ($key == $value || in_array($key, $valArray))) {
		$ophtml .= '<option selected="selected" value="' . $key . '" >' . $lab . '</option>';
	    }else{
		$ophtml .= '<option  value="' . $key . '" >' . $lab . '</option>';
	    }
	}
	$input = '<select class="ted3-element-setting" '.$multipleAttr.'  name="' . $name . '"  ' . $requiredAttr . ' size="' . $size . '" >'.$ophtml.'</select>';
	return '<tr><td><label>' . $label . '</label></td><td>' . $input . '</td></tr>';
    }

}

?>
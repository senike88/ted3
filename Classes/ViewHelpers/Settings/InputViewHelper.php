<?php

namespace DS\Ted3\ViewHelpers\Settings;

use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;

class InputViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments() {

        $this->registerArgument('name', 'string', '');
        $this->registerArgument('label', 'string', '', false, '');
        $this->registerArgument('value', 'string', '', false, '');
        $this->registerArgument('type', 'string', '', false, 'text');
        $this->registerArgument('min', 'string', '', false, '');
        $this->registerArgument('max', 'string', '', false, '');
        $this->registerArgument('maxlength', 'string', '', false, '');
        $this->registerArgument('checked', 'string', '', false, '');
        $this->registerArgument('required', 'string', '', false, '');
        $this->registerArgument('size', 'string', '', false, '');
        $this->registerArgument('default', 'string', '', false, '');
    }

    public function render() {


//        array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext

        $name = $this->arguments['name'];
        $label = $this->arguments['label'];
        $value = $this->arguments['value'];
        $type = $this->arguments['type'];
        $min = $this->arguments['min'];
        $max = $this->arguments['max'];
        $maxlength = $this->arguments['maxlength'];
        $checked = $this->arguments['checked'];
        $required = $this->arguments['required'];
        $size = $this->arguments['size'];
        $default = $this->arguments['default'];

        if (!($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE) {
            return null;
        }


        if ($this->viewHelperVariableContainer->exists(AbstractElementViewHelper::class, 'record')) {
            $record = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'record');
            $table = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'table');
            if (is_array($record)) {
                if (isset($record['ted3_settings'])) {
                    $ted3settings = json_decode($record['ted3_settings'], true);
//                    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($record['ted3_settings']);
                }

                if ($default && !@$ted3settings[$name] && @$ted3settings[$name] != $default) {
//                    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($record);
                    //Save Default
                    $ted3settings[$name] = $default;
                    $record['ted3_settings'] = json_encode($ted3settings);
                    $uid = $record['uid'];

                    $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
//                    var_dump($ted3settingsJson); exit;
                    $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'record', $record);

                    $data[$table][$uid]['ted3_settings'] = $record['ted3_settings'];

                    $tce->start($data, array());
                    $tce->process_datamap();
                }
            }
        }
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($ted3settings);
        if (!isset($ted3settings)) {
            $ted3settings = array();
        }
        if (@$ted3settings[$name] == $value || $checked) {
            $checkedAttr = 'checked="checked"';
        }

        if (!$value) {
            $value = @$ted3settings[$name];
//            if (!$value) {
//                $value = $default;
//            }
        }


        if ($required) {
            $requiredAttr = 'required="1"';
        }
        //    return "<b>test</b>";
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($vars); exit;
        @$input = '<input class="ted3-element-setting" type="' . $type . '" name="' . $name . '" ' . $checkedAttr . '" value="' . $value . '" ' . @$requiredAttr . ' size="' . $size . '" />';
        return '<tr><td><label>' . $label . '</label></td><td>' . $input . '</td></tr>';
    }

}

?>
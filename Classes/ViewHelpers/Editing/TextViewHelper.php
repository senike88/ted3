<?php

namespace DS\Ted3\ViewHelpers\Editing;

use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use DS\Ted3\Helper\PropertyHelper;

class TextViewHelper extends \DS\Ted3\ViewHelpers\AbstractTagBasedViewHelper {

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments() {
//        parent::initializeArguments();
        $this->registerArgument('field', 'string', '');
        $this->registerArgument('rte', 'int', '', false, 0);
        $this->registerArgument('parseFuncTSPath', 'string', '', false, "lib.ted3parseFunc_RTE");
        $this->registerArgument('default', 'string', '', false, "Text bearbeiten ...");
    }

    public function render() {

        $field = $this->arguments['field'];
        $rte = $this->arguments['rte'];
        $parseFuncTSPath = $this->arguments['parseFuncTSPath'];
        $default = $this->arguments['default'];


        $record = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'record');

        if (!isset($record)) {
            throw new \Exception("TextViewhelper: Record not found.");
        }
        if ($rte) {
            $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $content = $contentObject->parseFunc(PropertyHelper::getProperty($record, $field), array(), '< ' . $parseFuncTSPath);
        } else {
            $content = PropertyHelper::getProperty($record, $field);
        }

        $beLogin = ($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE;
        if ($beLogin) {
//            if($record['uid'] == 63 && $field=="ted3text1" && $_GET['nike'] == 1){
//                var_dump($content); exit;
//            }
            if ($rte) {
                $content = PropertyHelper::getProperty($record, $field);
            }
//            if($default && strlen( PropertyHelper::getProperty($record, $field)) < 1){
//                $content = ;
//            }
//            
            //TODO besser in das system integrieren, zb beim speichern
            return '<div data-widget="textedit" data-default="' . $default . '" data-rte="' . $rte . '" data-field=\'' . $field . '\' contenteditable="false">'
                    . $content .
                    '</div>';
        }

        return $content;
    }

}

?>

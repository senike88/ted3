<?php

namespace DS\Ted3\ViewHelpers\Element;

use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;
use DS\Ted3\ViewHelpers\Container\AbstractContainerViewHelper;
use DS\Ted3\Helper\PropertyHelper;

class RecordViewHelper extends AbstractElementViewHelper {

    public function initializeArguments() {
        $this->registerUniversalTagAttributes();

        $this->registerArgument('object', 'mixed', '');
        $this->registerArgument('table', 'string', '', false, "");
        $this->registerArgument('settings', 'array', '', false, array());
        $this->registerArgument('tag', 'string', '', false, 'div');
        $this->registerArgument('name', 'string', '', false, 'Record');
        $this->registerArgument('forceReload', 'int', '', false, 0);
        $this->registerArgument('linkfield', 'string', '', false, "");
        $this->registerArgument('disablefield', 'string', '', false, "hidden");
    }

    public function render() {
//        $object, $table = "", $settings = array(), $tag = "div", $name = "Record", $forceReload = 0, $linkfield = "", $disablefield = "hidden"

        $object = $this->arguments['object'];
        $table = $this->arguments['table'];
        $settings = $this->arguments['settings'];
        $tag = $this->arguments['tag'];
        $name = $this->arguments['name'];
        $forceReload = $this->arguments['forceReload'];
        $linkfield = $this->arguments['linkfield'];
        $disablefield = $this->arguments['disablefield'];



        if (!$table) {
            if ($this->viewHelperVariableContainer->exists(AbstractContainerViewHelper::class, 'table')) {
                $this->table = $this->viewHelperVariableContainer->get(AbstractContainerViewHelper::class, 'table');
            } else {
                throw new \Exception("No Table was set for record-viewhelper! Define either in container or for elements");
            }
        } else {
            $this->table = $table;
        }

        if ($this->viewHelperVariableContainer->exists(AbstractElementViewHelper::class, 'record')) {
            $this->parentRecord = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'record');
        }
        $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'record', $object);
//        if($this->templateVariableContainer->exists("ted3settings")){
//           $outerSettings = $this->templateVariableContainer->get("ted3settings");
//           $this->templateVariableContainer->remove("ted3settings");
////           $this->templateVariableContainer->add("ted3settings", $this->ted3settings);
//        }


        if ($this->viewHelperVariableContainer->exists(AbstractElementViewHelper::class, 'table')) {
            $this->parentTable = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'table');
        }
        $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'table', $this->table);


        if ($this->beLogin) {

            $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
            $currentlangId = $context->getPropertyFromAspect('language', 'id');

            $this->tag->addAttribute("data-uid", PropertyHelper::getProperty($object, 'uid'));

            if ($this->table == "pages") {

                if (isset($object['_PAGES_OVERLAY_UID']) && $object['sys_language_uid'] == $currentlangId) {
                    //get translated record
                    //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($object); exit;
                    $this->tag->addAttribute("data-origuid", $object['uid']);
                    $this->tag->addAttribute("data-uid", $object['_PAGES_OVERLAY_UID']);
                } else {

                    $this->tag->addAttribute("data-uid", $object['uid']);
                    if ($currentlangId > 0) {
                        $this->tag->addAttribute("data-translateable", 1);
                    }
                }
            } else { // other records
                if (isset($object['_LOCALIZED_UID']) && @$object['sys_language_uid'] == $currentlangId) {
                    //translated element
                    $this->tag->addAttribute("data-origuid", $object['uid']);
                    $this->tag->addAttribute("data-uid", $object['_LOCALIZED_UID']);
                } else if (@$object['sys_language_uid'] == $currentlangId && $currentlangId > 0) {
                    // untranslated Element from a diffrent language (added in backend)
                    $this->tag->addAttribute("data-uid", $object['uid']);
                } else {
                    //untranslated element from default language
                    $this->tag->addAttribute("data-uid", $object['uid']);

                    if ($currentlangId > 0) {
                        $this->tag->addAttribute("data-translateable", 1);
                    }
                }
            }
            $this->tag->addAttribute("data-hidden", PropertyHelper::getProperty($object, $disablefield));
            $this->tag->addAttribute("data-disablefield", $disablefield);
            $this->tag->addAttribute("data-element", "record");
            $this->tag->addAttribute("data-table", $this->table);
            $this->tag->addAttribute("data-forcereload", $forceReload);
            $this->tag->addAttribute("data-editingaccess", $this->editingAccess);
      

            if ($linkfield) {
                $this->tag->addAttribute("data-linkfield", $linkfield);
            }

            $this->elementSettings['name'] = $name;
            $this->elementSettings['move'] = 0;
            $this->elementSettings['buttonsort'] = 1;
            $this->elementSettings['buttonsort'] = 1;
            $this->elementSettings['copycutpaste'] = 0;


            $this->elementSettings = array_merge($this->elementSettings, $settings);

            $this->tag->addAttribute("data-settings", json_encode($this->elementSettings));
            $this->tag->setContent($this->renderChildren());
        } else {
            $this->tag->setContent($this->renderChildren());
        }
//        $this->templateVariableContainer->remove("ted3settings");
//        if($outerSettings){
//           $this->templateVariableContainer->add("ted3settings", $outerSettings);
//        }
        $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'table', $this->parentTable);
        $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'record', $this->parentRecord);


        return $this->tag->render();
    }

}

?>

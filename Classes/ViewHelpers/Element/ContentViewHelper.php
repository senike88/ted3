<?php

namespace DS\Ted3\ViewHelpers\Element;

class ContentViewHelper extends AbstractElementViewHelper {

    protected $table = "tt_content";

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments() {
        $this->registerUniversalTagAttributes();

        $this->registerArgument('object', 'array', '');
        $this->registerArgument('settings', 'array', '', false, array());
        $this->registerArgument('linkfield', 'string', '', false, '');
        $this->registerArgument('tag', 'string', '', false, 'div');
        $this->registerArgument('name', 'string', '', false, '');
        $this->registerArgument('forceReload', 'int', '', false, 0);
    }

    public function render() {

        $object = $this->arguments['object'];
        $settings = $this->arguments['settings'];
        $linkfield = $this->arguments['linkfield'];
        $tag = $this->arguments['tag'];
        $name = $this->arguments['name'];
        $forceReload = $this->arguments['forceReload'];

        $this->viewHelperVariableContainer->add(AbstractElementViewHelper::class, 'record', $object);
        $this->viewHelperVariableContainer->add(AbstractElementViewHelper::class, 'table', $this->table);
        if ($this->ted3settings) {
            $this->templateVariableContainer->add("ted3settings", $this->ted3settings);
        }



        if ($this->beLogin) {

            $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
            $currentlangId = $context->getPropertyFromAspect('language', 'id');

            $this->viewHelperVariableContainer->add(AbstractElementViewHelper::class, 'uid', $object['uid']);

            if (isset($object['_LOCALIZED_UID']) && $object['sys_language_uid'] == $currentlangId) {
                //get translated record
                $this->tag->addAttribute("data-origuid", $object['uid']);
                $this->tag->addAttribute("data-uid", $object['_LOCALIZED_UID']);
            } else if (@$object['sys_language_uid'] == $currentlangId && $currentlangId > 0) {
                // untranslated Element from a diffrent language (added in backend)
                $this->tag->addAttribute("data-uid", $object['uid']);
            } else {
                $this->tag->addAttribute("data-uid", $object['uid']);

                if ($currentlangId > 0) {
                    $this->tag->addAttribute("data-translateable", 1);
                }
            }

            //  $this->setProperties($object);
            $this->tag->addAttribute("data-hidden", $object['hidden']);
            if (isset($object['starttime']) && $object['starttime'] > 0 && $object['starttime'] > time()) {
                $this->tag->addAttribute("data-outofdate", 1);
                $addname = date("d.m.o, H:i", $object['starttime']);
                if ($object['endtime'] > 0) {
                    $addname .= " bis " . date("d.m.o, H:i", $object['endtime']);
                }
            }
            if (isset($object['endtime']) && $object['endtime'] > 0 && $object['endtime'] < time()) {
                $this->tag->addAttribute("data-outofdate", 1);
                $addname .= "bis " . date("d.m.o, H:i", $object['endtime']);
            }
            if ($addname) {
                $addname = " (" . $addname . ")";
            }
            $this->tag->addAttribute("data-element", "content");
            $this->tag->addAttribute("data-table", $this->table);
            $this->tag->addAttribute("data-cpid", $object['pid']);
            $this->tag->addAttribute("data-hidemobile", $object['ted3_hidemobile']);
            $this->tag->addAttribute("data-forcereload", $forceReload);
            $this->tag->addAttribute("data-editingaccess", $this->editingAccess);


//	    $this->tag->addAttribute("data-lang",$object['sys_language_uid']);
            if ($linkfield) {
                $this->tag->addAttribute("data-linkfield", $linkfield);
                $this->tag->addAttribute("data-typolink", $object[$linkfield]);
            }
            //	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($object); exit;


            if ($name) {
                $this->elementSettings['name'] = $name;
            } else {
                $this->elementSettings['name'] = ucfirst($object['CType']);
            }
            $this->elementSettings['name'] .= $addname;
            $this->elementSettings = array_merge($this->elementSettings, $settings);


            $this->tag->addAttribute("data-settings", json_encode($this->elementSettings));

            $this->tag->setContent($this->renderChildren());
            $this->viewHelperVariableContainer->remove(AbstractElementViewHelper::class, 'uid');
        } else {
            $this->tag->setContent($this->renderChildren());
        }
        $this->viewHelperVariableContainer->remove(AbstractElementViewHelper::class, 'table');
        $this->viewHelperVariableContainer->remove(AbstractElementViewHelper::class, 'record');
        if ($this->ted3settings) {
            $this->templateVariableContainer->remove("ted3settings");
        }
        return $this->tag->render();
    }

}

?>

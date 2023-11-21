<?php

namespace DS\Ted3\ViewHelpers\Container;

use DS\Ted3\ViewHelpers\Container\AbstractContainerViewHelper;

class RecordViewHelper extends AbstractContainerViewHelper {

    public function initializeArguments() {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', '');
        $this->registerArgument('storagePid', 'integer', '', false, 0);
        $this->registerArgument('name', 'string', '', false, "");
        $this->registerArgument('forceReload', 'integer', '', false, 0);
        $this->registerArgument('allowOnlyEmptyAddzone', 'integer', '', false, 0);
        $this->registerArgument('access', 'string', '', false, "");
    }

    public function render() {
//        $table, $storagePid = 0, $name = "", $forceReload = 0, $allowOnlyEmptyAddzone = 0, $access = ""

        $table = $this->arguments['table'];
        $storagePid = $this->arguments['storagePid'];
        $name = $this->arguments['name'];
        $forceReload = $this->arguments['forceReload'];
        $allowOnlyEmptyAddzone = $this->arguments['allowOnlyEmptyAddzone'];
        $access = $this->arguments['access'];

        // $templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
        // $parentTable = InheritViewhelperVar::$vars['table'];
        if ($this->viewHelperVariableContainer->exists(AbstractContainerViewHelper::class, 'table')) {
            $this->parentContainerTable = $this->viewHelperVariableContainer->get(AbstractContainerViewHelper::class, 'table');
        }
        $this->viewHelperVariableContainer->addOrUpdate(AbstractContainerViewHelper::class, 'table', $table);

        if ($this->editingAccess) {
//            InheritViewhelperVar::$vars['table'] = $table;
//            InheritViewhelperVar::$vars['field'] = $field;
            $this->tag->addAttribute('data-pid', $storagePid);
            $this->tag->addAttribute('data-container', 'record');
            $this->tag->addAttribute('data-table', $table);
            $this->tag->addAttribute('data-name', $name);
            $this->tag->addAttribute("data-forcereload", $forceReload);

            $settings = array(
                'addzone' => array('files' => 0, 'elements' => 0, 'cleanrecords' => 1),
                'allowOnlyEmptyAddzone' => $allowOnlyEmptyAddzone
            );

            $this->tag->addAttribute("data-settings", json_encode($settings));
        }


        $this->tag->setContent($this->renderChildren());
        if ($this->parentContainerTable) {
            $this->viewHelperVariableContainer->addOrUpdate(AbstractContainerViewHelper::class, "table", $this->parentContainerTable);
        } else {
            $this->viewHelperVariableContainer->remove(AbstractContainerViewHelper::class, "table");
        }

        return $this->tag->render();
    }

}

?>
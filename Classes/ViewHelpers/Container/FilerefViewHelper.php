<?php

namespace DS\Ted3\ViewHelpers\Container;

use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;

class FilerefViewHelper extends AbstractContainerViewHelper {

    public function initializeArguments() {
//        parent::initializeArguments();
        $this->registerArgument('field', 'string', '');
        $this->registerArgument('access', 'string', '', false, '');
        $this->registerArgument('forceReload', 'integer', '', false, 0);
    }

    public function render() {

        $field = $this->arguments['field'];
        $access = $this->arguments['access'];
        $forceReload = $this->arguments['forceReload'];


        if ($this->editingAccess) {
            // $parentTable = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, 'table');
            //$this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'table', "sys_file_reference");
            $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'reffield', $field);


            $this->tag->addAttribute('data-container', 'fileref');
            $this->tag->addAttribute('data-field', $field);
            $this->tag->addAttribute("data-forcereload", $forceReload);

            $settings = array(
                'addzone' => array_merge($this->addzone, array('elements' => 0, 'fromfiles' => 0)),
                'allowOnlyEmptyAddzone' => 0
            );



            $this->tag->addAttribute("data-settings", json_encode($settings));



            $this->tag->setContent($this->renderChildren());
            $this->viewHelperVariableContainer->remove(AbstractElementViewHelper::class, 'reffield');

            //  $this->viewHelperVariableContainer->addOrUpdate(AbstractElementViewHelper::class, 'table', $parentTable);

            return $this->tag->render();
        }


        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }

}

?>
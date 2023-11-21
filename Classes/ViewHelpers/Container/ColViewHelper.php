<?php

namespace DS\Ted3\ViewHelpers\Container;

class ColViewHelper extends AbstractContainerViewHelper {

    public function initializeArguments() {
        parent::initializeArguments();
        $this->registerArgument('colpos', 'integer', '');
        $this->registerArgument('container', 'integer', '', false, 0);
        $this->registerArgument('addzone', 'array', '', false, array());
        $this->registerArgument('name', 'string', '', false, "");
        $this->registerArgument('allowOnlyEmptyAddzone', 'integer', '', false, 0);
        $this->registerArgument('buttonsorting', 'integer', '', false, 0);
        $this->registerArgument('forceReload', 'integer', '', false, 0);
        $this->registerArgument('access', 'string', '', false, "");
    }

    public function render() {


        $colpos = $this->arguments['colpos'];
        $container = $this->arguments['container'];
        $addzone = $this->arguments['addzone'];
        $allowOnlyEmptyAddzone = $this->arguments['allowOnlyEmptyAddzone'];
        $buttonsorting = $this->arguments['buttonsorting'];
        $name = $this->arguments['name'];
        $forceReload = $this->arguments['forceReload'];
        $access = $this->arguments['access'];


        if (@$this->editingAccess) {
            // Check if content-slide


            $this->tag->addAttribute('data-container', 'content');
            $this->tag->addAttribute('data-pid', $GLOBALS['TSFE']->page['uid']);
            $this->tag->addAttribute('data-colpos', $colpos);
            $this->tag->addAttribute('data-parent', $container);
            $this->tag->addAttribute('data-buttonsorting', $buttonsorting);
            $this->tag->addAttribute('data-name', $name);
            $this->tag->addAttribute('data-forcereload', $forceReload);



            $settings = array(
                'addzone' => array_merge($this->addzone, $addzone),
                'allowOnlyEmptyAddzone' => $allowOnlyEmptyAddzone
            );

            $this->tag->addAttribute("data-settings", json_encode($settings));
            $this->tag->setContent($this->renderChildren());
            // $this->viewHelperVariableContainer->remove(AbstractContainerViewHelper::class,'table');
        } else {
            if ($this->beUser) {
                $this->tag->addAttribute('data-container', 'noteditable');
            }
            $this->tag->setContent($this->renderChildren());
        }
        return $this->tag->render();
    }

}

?>

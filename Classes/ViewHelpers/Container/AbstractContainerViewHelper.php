<?php

namespace DS\Ted3\ViewHelpers\Container;

class AbstractContainerViewHelper extends \DS\Ted3\ViewHelpers\AbstractTagBasedViewHelper {

    protected $addzone = array(
        'files' => 1,
        'fromfiles' => 1,
        'elements' => 1
    );

   public function initialize() {
        parent::initialize();
        $this->tag->forceClosingTag(true);

        $this->beUser = ($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE;
        if ($this->beUser) {
            $this->editingAccess = true;
            if ($this->arguments['access'] == "admins" && $GLOBALS['BE_USER']->user['admin'] != true) {
                $this->editingAccess = false;
            } else if (strpos($this->arguments['access'], ":")) {
                $t = explode(":", $this->arguments['access']);
                $ids = explode(",", $t[1]);
                if ($t[0] == "users" && !in_array($GLOBALS['BE_USER']->user['uid'], $ids)) {
                    $this->editingAccess = false;
                }
                if ($t[0] == "groups" && !in_array($GLOBALS['BE_USER']->user['usergroup'], $ids)) {
                    $this->editingAccess = false;
                }
            }
            
            if($GLOBALS['BE_USER']->user['admin']){
                    $this->editingAccess = true;
            }
        }

    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments() {

        $this->registerUniversalTagAttributes();
    }

}

?>

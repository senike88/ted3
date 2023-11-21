<?php

namespace DS\Ted3\ViewHelpers\Element;

use DS\Ted3\Helper\PropertyHelper;

class AbstractElementViewHelper extends \DS\Ted3\ViewHelpers\AbstractTagBasedViewHelper {

    protected $elementSettings = array(
        'save' => 1,
        't3edit' => 1,
        'hide' => 1,
        'hidemobile' => 0,
        'delete' => 1,
        'move' => 1,
        'clone' => 1,
        'copycutpaste' => 1,
        'buttonsort' => 0,
        'edithelper' => 0,
        'disableChildren' => 0
    );
    protected $dataArray;

    public function initialize() {
        parent::initialize();
        $this->tag->forceClosingTag(true);
        if ($this->hasArgument('tag')) {
            $this->tag->setTagName($this->arguments['tag']);
        }
        $this->beLogin = ($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE;

        if ($this->beLogin) {
            $uid = PropertyHelper::getProperty($this->arguments['object'], "uid");
            if (!is_numeric($uid) || !$uid) {
                throw new \Exception("No valid object or array given for " . get_class($this));
            }
            
            if(!isset($this->arguments['elementaccess'])){
                $this->arguments['elementaccess'] = "";
            }

            $this->editingAccess = 1;
            if ($this->arguments['elementaccess'] == "admins" && $GLOBALS['BE_USER']->user['admin'] != true) {
                $this->editingAccess = 0;
            } else if (strpos($this->arguments['elementaccess'], ":")) {
                $t = explode(":", $this->arguments['elementaccess']);
                $ids = explode(",", $t[1]);
                if ($t[0] == "users" && !in_array($GLOBALS['BE_USER']->user['uid'], $ids)) {
                    $this->editingAccess = 0;
                }
                if ($t[0] == "groups" && !in_array($GLOBALS['BE_USER']->user['usergroup'], $ids)) {
                    $this->editingAccess = 0;
                }
            }
        }

        if (is_array($this->arguments['object']) && isset($this->arguments['object']['ted3_settings'])) {
            $this->ted3settings = json_decode($this->arguments['object']['ted3_settings'], true);
        } else if (is_object($this->arguments['object']) && isset($this->arguments['object']->ted3_settings)) {
            $this->ted3settings = json_decode($this->arguments['object']->ted3_settings, true);
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

    protected function getProperty($obj, $key) {
        if (is_array($obj)) {
            if (isset($obj[$key])) {
                return $obj[$key];
            } else {
                throw new \Exception("Element-Viewhelper: Field " . $key . " not found");
            }
        } else if (is_object($obj)) {
            $getter = "get" . ucfirst($key);
            if (method_exists($obj, $getter)) {
                return $obj->$getter();
            } else {
                throw new \Exception("Element-Viewhelper: Field " . $key . " not found or accessible");
            }
        }
    }

}

?>

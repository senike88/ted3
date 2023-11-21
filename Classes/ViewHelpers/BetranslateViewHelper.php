<?php

namespace DS\Ted3\ViewHelpers;

class BetranslateViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    /**
     * Initialize arguments
     */
    public function initializeArguments() {
        $this->registerArgument('default', 'string', '');
        $this->registerArgument('de', 'string', '');
        $this->registerArgument('en', 'string', '');
    }

    public function render() {
        // $default = $this->arguments['default'];
        // $de = $this->arguments['de'];
        $currentBeLanguage = strtolower($GLOBALS['LANG']->lang);

        if ($currentBeLanguage != "de" && $currentBeLanguage != "en") {
            $currentBeLanguage = "default";
        }

        return $this->arguments[$currentBeLanguage];
    }

}

?>

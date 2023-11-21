<?php

namespace DS\Ted3\ViewHelpers;

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;

class MainViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function render() {


        //echo TYPO3_version; exit;
        //if(TYPO3_version > 8.9 ){
        $typo3db_legacyIsLoadet = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('typo3db_legacy');
        if (!$typo3db_legacyIsLoadet) {
            throw new \Exception("TED³: The Extension typo3db_legacy is required.");
        }

        //Shortcut-Button
        if ($GLOBALS['TSFE']->page['shortcut_mode'] || $GLOBALS['TSFE']->page['shortcut']) {
            //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump("test"); exit;
            if (@$GLOBALS['TSFE']->page['_originalDoktype'] == 4) {
              //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']->page); exit;
                $shortcutPage = $GLOBALS['TSFE']->sys_page->getPageShortcut(
                        (string) $GLOBALS['TSFE']->page['shortcut'], (string) $GLOBALS['TSFE']->page['shortcut_mode'], $GLOBALS['TSFE']->page['uid']
                );
              //  echo "test"; exit;
            }
        }
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']->id ); exit;
        $pagetranslations = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'pages', 'l10n_parent = ' . $GLOBALS['TSFE']->id . " and hidden=0 and deleted=0", "", "", "", "sys_language_uid");
        //      \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pagetranslations); exit;
        //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( $GLOBALS['LANG']); exit;
        if (!isset($GLOBALS['LANG'])) {
            $GLOBALS['LANG'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Lang\LanguageService');
        }


        $siteConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Configuration\SiteConfiguration');
        $iconFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Imaging\IconFactory');
        
        $currentSite = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
        $siteLangs = $currentSite->getAllLanguages();
        
        $syslangs = array();
         //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($siteLangs[0]->getFallbackType() ); exit;
        foreach ($siteLangs as $i => $siteLang) {

            $syslangs[$i] = array(
                'uid' => $siteLang->getLanguageId(),
                'title' => $siteLang->getTitle(),
                'iconmarkup' => $iconFactory->getIcon($siteLang->getFlagIdentifier())->getMarkup(),
                'fallbackType' => $siteLang->getFallbackType(),
            );
            //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(get_class_methods( $iconFactory->getIcon($siteLang->getFlagIdentifier())) ); exit;
        }


        foreach ($syslangs as &$lang) {
            $syslanguid = $lang['uid'];

            if (isset($pagetranslations[$syslanguid])) {

                $lang['hasPageTranslation'] = 1;
                $lang['pageTransUid'] = @$pagetranslations['uid'];
            }
            if ($syslanguid == 0) {
                $lang['hasPageTranslation'] = 1;
            }
        }
        //
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($syslangs); exit;


        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
        $currentlangId = $context->getPropertyFromAspect('language', 'id');


        // echo "sadf";
       // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($currentlangId); exit;
        $data = array(
            "pid" => $GLOBALS['TSFE']->id,
            "deflang" => array('uid' => 0, 'flag' => 'deflang.svg'),
            "currentlangFlag" => $syslangs[$currentlangId]['iconmarkup'],
            "langFallbackType" => $syslangs[$currentlangId]['fallbackType'],
            "currentlangId" => $currentlangId,
            "currentHasPageTranslation" => 0,
            "shortcutPage" => @$shortcutPage['uid'],
            "origDoktype" => @$GLOBALS['TSFE']->page['_originalDoktype'],
            "imageFileExtensions" => explode(",", $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])
        );
        
        $data['syslangs'] = array_reverse($syslangs);
        
        if(count( @$data['syslangs']) < 2){
            $data['onlyOneSyslang'] = 1;
        }
     

        if (isset($pagetranslations[$data['currentlangId']])) {
            $data['currentHasPageTranslation'] = 1;
        } else {
            $data['currentHasPageTranslation'] = 0;
        }
        $data['belang'] = $GLOBALS['LANG']->lang;

        //@todo -> just default?
        if ($data['belang'] != "de" && $data['belang'] != "en") {
            $data['belang'] = "default";
        }
//        if($_GET['ted3_showhidden'] == 1){
//            $data['showhidden'] = 1;
//        }
        //$TYPO3_MODE = "BE";
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump( TYPO3_MODE); exit;
        $this->templateVariableContainer->add("fedata", $data);
        $this->templateVariableContainer->add("fedataJSON", json_encode($data, JSON_FORCE_OBJECT));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove("fedata");
        $this->templateVariableContainer->remove("fedataJSON");
        //$TYPO3_MODE = "FE";

        return $content;
    }

}

?>

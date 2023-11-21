<?php

//

namespace DS\Ted3\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

class UcViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    /**
     * Initialize arguments
     */
    public function initializeArguments() {
        $this->registerArgument('str', 'string', '');
    }

    public function render() {
        $str = $this->arguments['str'];

        return strtoupper($str);
    }

//      /**
//     *
//     * @param array $arguments
//     * @param \Closure $renderChildrenClosure
//     * @param RenderingContextInterface $renderingContext
//     * @return string
//     */
//    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
//    {
//        
//      //  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($renderingContext); exit;
//        $str = $arguments['str'];
//         return strtoupper($str);
//    }
}

?>

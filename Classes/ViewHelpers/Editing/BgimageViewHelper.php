<?php

namespace DS\Ted3\ViewHelpers\Editing;

use TYPO3\CMS\Core\Resource\FileReference;
use DS\Ted3\ViewHelpers\Element\AbstractElementViewHelper;
use DS\Ted3\Helper\PropertyHelper;

class BgimageViewHelper extends \DS\Ted3\ViewHelpers\AbstractTagBasedViewHelper {

    public function initialize() {
        parent::initialize();
        $this->tag->forceClosingTag(true);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     */
    public function injectImageService(\TYPO3\CMS\Extbase\Service\ImageService $imageService) {
        $this->imageService = $imageService;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments() {

        $this->registerUniversalTagAttributes();

        $this->registerArgument('image', 'FileReference', '');
        $this->registerArgument('field', 'string', '', false, '');
        $this->registerArgument('bgposition', 'string', '', false, 'center center');
        $this->registerArgument('bgsize', 'string', '', false, 'cover');
        $this->registerArgument('mobileWidth', 'string', '', false, '600px');
    }

    public function render() {

        $image = $this->arguments['image'];
        $field = $this->arguments['field'];
        $bgposition = $this->arguments['bgposition'];
        $bgsize = $this->arguments['bgsize'];
        $mobileWidth = $this->arguments['mobileWidth'];



        $this->tag->setTagName("div");
        $beLogin = ($GLOBALS['TSFE']->beUserLogin === 1 || $GLOBALS['TSFE']->beUserLogin == true ) ? TRUE : FALSE;
        $parentRecord = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, "record");

        if ($beLogin) {
            if (!$field) {
                if ($this->viewHelperVariableContainer->exists(AbstractElementViewHelper::class, "reffield")) {
                    $field = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, "reffield");
                } else {
                    throw new \Exception("Editing: BgImage-Viewhelper no field set");
                }
            }
        }
        if (!$bgposition) {
            $bgposition = "center center";
        }

        //From FilerefViewhelper
        if (!$image instanceof FileReference) {

            if ($parentRecord instanceof FileReference) {
                $image = $parentRecord;
            }
        }
        //From Repository
        if (!$image instanceof FileReference) {

            $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');

            $parentTable = $this->viewHelperVariableContainer->get(AbstractElementViewHelper::class, "table");

            if (@$parentRecord['_LOCALIZED_UID']) {
                $files = $fileRepository->findByRelation($parentTable, $field, $parentRecord['_LOCALIZED_UID']);
            } else {
                $files = $fileRepository->findByRelation($parentTable, $field, PropertyHelper::getProperty($parentRecord, "uid"));
            }

            $image = @$files[0];
        }
        if (!$image instanceof FileReference) {
            if ($beLogin) {
                $content = '<div data-widget="image" style="min-height:30px;" class="ted3-image-placeholder" data-field="' . $field . '" >' . $this->renderChildren() . '</div>';
                $this->tag->setContent($content);
                $this->tag->addAttribute('style', $this->arguments['style']);
                return $this->tag->render();
            }
        } else {
            $imgUrl = $image->getPublicUrl();

            if (function_exists('user_isMobileDevice') && user_isMobileDevice()) { 
                $processingInstructions = [
                    'width' => $mobileWidth,
                ];


                $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
                $imgUrl = $this->imageService->getImageUri($processedImage, false);
            }

            $this->tag->addAttribute('style', "min-height:30px;background-repeat:no-repeat;background-position:$bgposition;background-image:url(" . $imgUrl . ");background-size:$bgsize; " . $this->arguments['style']);

            if ($beLogin) {
                $this->tag->addAttribute('data-uid', $image->getUid());
                $this->tag->addAttribute('data-widget', "image");
                $this->tag->addAttribute('data-field', $field);
            }
        }

        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }

}

?>

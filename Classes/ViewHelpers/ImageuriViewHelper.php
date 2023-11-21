<?php

namespace DS\Ted3\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileReference;

class ImageuriViewHelper extends \DS\Ted3\ViewHelpers\AbstractViewHelper {

    public function initializeArguments() {
//        parent::initializeArguments();
        $this->registerArgument('image', 'TYPO3\CMS\Core\Resource\FileReference', '');
        $this->registerArgument('mobileWidth', 'string', '', false, "600px");
    }

    /**
     * @var \TYPO3\CMS\Extbase\Service\ImageService
     */
    protected $imageService;

    /**
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     */
    public function injectImageService(\TYPO3\CMS\Extbase\Service\ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function render() {

        $image = $this->arguments['image'];
        $mobileWidth = $this->arguments['mobileWidth'];

        $imgUrl = $image->getPublicUrl();
        if (function_exists('user_isMobileDevice') && user_isMobileDevice()) {
            //  $imgUrl = \TYPO3\CMS\Fluid\ViewHelpers\Uri\ImageViewHelper::render(null, $image, $mobileWidth);

            $processingInstructions = [
                'width' => $mobileWidth
            ];

            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
            $imgUrl = $this->imageService->getImageUri($processedImage, false);
        }
        return $imgUrl;
    }

}

?>

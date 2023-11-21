<?php

namespace DS\Ted3\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

    public function __construct() {
        parent::__construct();
        $this->imageService = GeneralUtility::makeInstance(ImageService::class);
         // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->imageService );
        //exit;
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

           // $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
            $imgUrl = $this->imageService->getImageUri($processedImage, false);
        }
        return $imgUrl;
    }

}

?>

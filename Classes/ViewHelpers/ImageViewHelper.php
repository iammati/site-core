<?php

declare(strict_types=1);

namespace Site\Core\ViewHelpers;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class ImageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'img';

    /**
     * @var \TYPO3\CMS\Extbase\Service\ImageService
     */
    protected $imageService;

    public function injectImageService(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerUniversalTagAttributes();

        $this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', false);
        $this->registerTagAttribute('ismap', 'string', 'Specifies an image as a server-side image-map. Rarely used. Look at usemap instead', false);
        $this->registerTagAttribute('longdesc', 'string', 'Specifies the URL to a document that contains a long description of an image', false);
        $this->registerTagAttribute('usemap', 'string', 'Specifies an image as a client-side image-map', false);
        $this->registerTagAttribute('loading', 'string', 'Native lazy-loading for images property. Can be "lazy", "eager" or "auto"', false);

        $this->registerArgument('src', 'string', 'a path to a file, a combined FAL identifier or an uid (int). If $'.'treatIdAsReference is set, the integer is considered the uid of the sys_file_reference record. If you already got a FAL object, consider using the $'.'image parameter instead', false, '');
        $this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record', false, false);
        $this->registerArgument('image', 'object', 'a FAL object');
        $this->registerArgument('crop', 'string|bool', 'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');
        $this->registerArgument('fileExtension', 'string', 'Custom file extension to use');

        $this->registerArgument('width', 'string', 'width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('height', 'string', 'height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('minWidth', 'int', 'minimum width of the image');
        $this->registerArgument('minHeight', 'int', 'minimum height of the image');
        $this->registerArgument('maxWidth', 'int', 'maximum width of the image');
        $this->registerArgument('maxHeight', 'int', 'maximum height of the image');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);
    }

    /**
     * Resizes a given image (if required) and renders the respective img tag.
     *
     * @see https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Image/
     *
     * @return string Rendered tag
     *
     * @throws Exception
     */
    public function render()
    {
        $src = (string) $this->arguments['src'];

        if (
            ($src === '' && $this->arguments['image'] === null) ||
            ($src !== '' && $this->arguments['image'] !== null)
        ) {
            throw new Exception('You must either specify a string src or a File object.', 1382284106);
        }

        // A URL was given as src, this is kept as is, and we can only scale
        if ($src !== '' && preg_match('/^(https?:)?\/\//', $src)) {
            $this->tag->addAttribute('src', $src);

            if (isset($this->arguments['width'])) {
                $this->tag->addAttribute('width', $this->arguments['width']);
            }

            if (isset($this->arguments['height'])) {
                $this->tag->addAttribute('height', $this->arguments['height']);
            }
        } else {
            try {
                $image = $this->imageService->getImage($src, $this->arguments['image'], (bool) $this->arguments['treatIdAsReference']);
                $cropString = $this->arguments['crop'];

                if (
                    $cropString === null &&
                    $image->hasProperty('crop') &&
                    $image->getProperty('crop')
                ) {
                    $cropString = $image->getProperty('crop');
                }

                $cropVariantCollection = CropVariantCollection::create((string) $cropString);
                $cropVariant = $this->arguments['cropVariant'] ?: 'default';
                $cropArea = $cropVariantCollection->getCropArea($cropVariant);

                $processingInstructions = [
                    'width' => $this->arguments['width'],
                    'height' => $this->arguments['height'],
                    'minWidth' => $this->arguments['minWidth'],
                    'minHeight' => $this->arguments['minHeight'],
                    'maxWidth' => $this->arguments['maxWidth'],
                    'maxHeight' => $this->arguments['maxHeight'],
                    'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
                ];

                if (!empty($this->arguments['fileExtension'] ?? '')) {
                    $processingInstructions['fileExtension'] = $this->arguments['fileExtension'];
                }

                $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
                $imageUri = $this->imageService->getImageUri($processedImage, $this->arguments['absolute']);

                if (!$this->tag->hasAttribute('data-focus-area')) {
                    $focusArea = $cropVariantCollection->getFocusArea($cropVariant);

                    if (!$focusArea->isEmpty()) {
                        $this->tag->addAttribute('data-focus-area', $focusArea->makeAbsoluteBasedOnFile($image));
                    }
                }

                $this->tag->addAttribute('src', $imageUri);
                $this->tag->addAttribute('width', $processedImage->getProperty('width'));
                $this->tag->addAttribute('height', $processedImage->getProperty('height'));

                // The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
                if (empty($this->arguments['alt'])) {
                    $this->tag->addAttribute('alt', $image->hasProperty('alternative') ? $image->getProperty('alternative') : '');
                }
                $title = $image->hasProperty('title') ? $image->getProperty('title') : '';
                if (empty($this->arguments['title']) && $title !== '') {
                    $this->tag->addAttribute('title', $title);
                }
            } catch (ResourceDoesNotExistException $e) {
                // thrown if file does not exist
                throw new Exception($e->getMessage(), 1509741911, $e);
            } catch (\UnexpectedValueException $e) {
                // thrown if a file has been replaced with a folder
                throw new Exception($e->getMessage(), 1509741912, $e);
            } catch (\RuntimeException $e) {
                // RuntimeException thrown if a file is outside of a storage
                throw new Exception($e->getMessage(), 1509741913, $e);
            } catch (\InvalidArgumentException $e) {
                // thrown if file storage does not exist
                throw new Exception($e->getMessage(), 1509741914, $e);
            }
        }

        $src = $this->tag->getAttribute('src');

        $type = $image->getMimeType();
        $data = $image->getContents();

        $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);

        $this->tag->removeAttribute('src');
        $this->tag->addAttribute('src', $base64);

        return $this->tag->render();
    }
}

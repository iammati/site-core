<?php

declare(strict_types=1);

namespace Site\Core\ViewHelpers;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This ViewHelper basically gets the original file's content.
 * Maybe @todo instead of the original fetching the referenced one
 * since backend users might crop and then the cropped-view's content
 * should be shown in the FE?
 */
class Image2svgViewHelper extends AbstractViewHelper
{
    /**
     * Initialization of required arguments for this ViewHelper.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('svg', 'object', 'A FAL object', true);
    }

    /**
     * The actual render logic whenever this VH gets called.
     *
     * @return void
     */
    public function render()
    {
        /** @var FileReference */
        $svg = $this->arguments['svg'];

        return $svg->getOriginalFile()->getContents();
    }
}

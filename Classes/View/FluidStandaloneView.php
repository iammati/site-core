<?php

namespace Site\Core\View;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\View\TemplatePaths;
use TYPO3Fluid\Fluid\View\TemplateView;

class FluidStandaloneView
{
    /**
     * @param array|string $rootPathsOrTemplatePath
     *
     * @return TemplateView
     */
    public static function create($rootPathsOrTemplatePath)
    {
        /** @var TemplateView */
        $view = GeneralUtility::makeInstance(TemplateView::class);

        /** @var RenderingContext */
        $renderingContext = GeneralUtility::makeInstance(RenderingContext::class, $view);

        /** @var TemplatePaths */
        $templatePaths = GeneralUtility::makeInstance(TemplatePaths::class);

        if (is_array($rootPathsOrTemplatePath)) {
            foreach ($rootPathsOrTemplatePath as $type => $paths) {
                $method = 'set' . ucfirst($type) . 'RootPaths';

                if (!is_array($paths)) {
                    throw new Exception('The passed paths for ' . $type . ' must be an array, string given!');
                }

                foreach ($paths ?? [] as $i => $path) {
                    $paths[$i] = GeneralUtility::getFileAbsFileName($path);
                }

                $templatePaths->{$method}($paths);
            }
        } else {
            $templatePaths->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName($rootPathsOrTemplatePath)
            );
        }

        $renderingContext->setTemplatePaths($templatePaths);
        $view->setRenderingContext($renderingContext);

        return $view;
    }
}

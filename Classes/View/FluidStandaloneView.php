<?php

declare(strict_types=1);

namespace Site\Core\View;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\View\TemplatePaths;
use TYPO3Fluid\Fluid\View\TemplateView;

class FluidStandaloneView
{
    /** @param array|string $rootPathsOrTemplatePath */
    public static function create($rootPathsOrTemplatePath, string $controllerName = '', string $actionName = ''): TemplateView
    {
        /** @var TemplateView */
        $view = GeneralUtility::makeInstance(TemplateView::class);

        /** @var RenderingContext */
        $renderingContext = GeneralUtility::makeInstance(RenderingContext::class, $view);


        /** @var TemplatePaths */
        $templatePaths = GeneralUtility::makeInstance(TemplatePaths::class);

        if (is_array($rootPathsOrTemplatePath)) {
            foreach ($rootPathsOrTemplatePath as $type => $paths) {
                $method = 'set'.ucfirst($type).'RootPaths';

                if (!is_array($paths)) {
                    throw new Exception('The passed paths for '.$type.' must be an array, string given!');
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
        $renderingContext->setControllerName($controllerName);
        $renderingContext->setControllerAction($actionName);
        $view->setRenderingContext($renderingContext);

        return $view;
    }
}

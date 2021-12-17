<?php

declare(strict_types=1);

namespace Site\Core\View;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\View\TemplatePaths;

class TYPO3StandaloneView
{
    /** @param array|string $rootPathsOrTemplatePath */
    public static function create($rootPathsOrTemplatePath, string $controllerName, string $actionName): StandaloneView
    {
        $view = new StandaloneView();

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

        $view->getRenderingContext()->setTemplatePaths($templatePaths);
        $view->getRenderingContext()->setControllerName($controllerName);
        $view->getRenderingContext()->setControllerAction($actionName);

        return $view;
    }
}

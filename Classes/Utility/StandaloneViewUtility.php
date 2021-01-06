<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StandaloneViewUtility
{
    /**
     * Renders a standalone view (template) by the given rootPaths.
     *
     * @param array  $rootPaths The paths (e.g. ['Templates' => 'EXT:xyz/Resources...', 'Partials' => '...', 'Layouts' => '...'])
     * @param string $template  The template name to be used
     * @param array  $assign    Set/Data for assigned to the template
     * @param bool   $echo      Simply echo the rendered view (which returns then a true) or return (the HTML) back
     *
     * @return string|array|void
     *
     * @throws Exception
     */
    public static function render(
        array $rootPaths = null,
        string $template = null,
        array $assign = null,
        bool $echo = false
    ) {
        $exWord = '';

        if (is_null($rootPaths)) {
            $exWord = 'any Template-/Layout-/PartialRootPaths';
        }

        if (is_null($template)) {
            $exWord = 'a Template-File';
        }

        if ($exWord != '') {
            throw new Exception('StandaloneViewUtility - can\'t render a view without $exWord');
        }

        $standaloneView = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);

        foreach ($rootPaths as $type => $path) {
            if ($type == 'Templates') {
                $standaloneView->setTemplateRootPaths([
                    GeneralUtility::getFileAbsFileName($path),
                ]);
            }

            if ($type == 'Layouts') {
                $standaloneView->setLayoutRootPaths([
                    GeneralUtility::getFileAbsFileName($path),
                ]);
            }

            if ($type == 'Partials') {
                $standaloneView->setPartialRootPaths([
                    GeneralUtility::getFileAbsFileName($path),
                ]);
            }
        }

        $standaloneView->setTemplate($template);

        if (!is_null($assign)) {
            $standaloneView->assignMultiple($assign);
        }

        if ($echo) {
            echo $standaloneView->render();

            return true;
        }

        return $standaloneView->render();
    }
}

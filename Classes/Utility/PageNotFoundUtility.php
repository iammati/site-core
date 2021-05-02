<?php

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;

class PageNotFoundUtility
{
    public static function display(string $message = '')
    {
        $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
            serverRequest(),
            $message,
            ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]
        );

        throw new ImmediateResponseException($response);
    }
}

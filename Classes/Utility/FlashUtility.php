<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlashUtility
{
    public function message($header = '', $bodytext = '', $severity = FlashMessage::OK, $storeInSess = false): void
    {
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $bodytext,
            $header,
            $severity,
            $storeInSess
        );

        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);

        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }
}

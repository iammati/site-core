<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlashUtility
{
    /**
     * Throws a TYPO3 flash message.
     *
     * @param string                                 $header
     * @param string                                 $bodytext
     * @param \TYPO3\CMS\Core\Messaging\FlashMessage $severity
     *                                                            -  const NOTICE = -2;
     * @param bool                                   $storeInSess
     */
    public function message($header = '', $bodytext = '', $severity, $storeInSess = false)
    {
        $message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessage::class,
            $bodytext,
            $header,
            $severity,
            $storeInSess
        );

        $objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $flashMessageService = $objectManager->get(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);

        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }
}

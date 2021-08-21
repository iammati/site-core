<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use Exception;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom Exception-Utility class.
 *
 * This class was designed to throw an exception if:
 * - environment is in Developer-Context.
 *
 * Else, e.g. in a Production-Context it will:
 * - log the thrown exception with an additional severity-level
 * - and optionally a Live Backend Notification to see on which page what just happened.
 */
class ExceptionUtility extends Exception
{
    /**
     * Throws a custom exception depending in the environment context.
     *
     * @param string $message                 the log message itself
     * @param int    $level                   Severity-Level. Possible values are:
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::EMERGENCY
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::ALERT
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::CRITICAL
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::ERROR
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::WARNING
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::NOTICE
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::INFO
     *                                        - \TYPO3\CMS\Core\Log\LogLevel::DEBUG
     * @param array  $data                    optional parameter, can contain additional data, which is added to the log record in the form of an array
     * @param bool   $liveBackendNotification Optional.
     *                                        - If it's true it'll notify the backend, through the SocketComponent::class with a live-notification - only if TYPO3_MODE equals FE.
     *
     * @throws Exception
     */
    public static function throw(
        string $message,
        int $code = -1,
        $level = \TYPO3\CMS\Core\Log\LogLevel::ERROR,
        array $data = [],
        bool $liveBackendNotification = false
    ) {
        $context = Environment::getContext();

        if ($context->isDevelopment()) {
            throw new Exception($message, $code);
        }

        if ($liveBackendNotification && TYPO3_MODE == 'FE') {
            // @todo make live backend notification
        }

        $logger = GeneralUtility::makeInstance(Logger::class, ...[
            $message,
            time(),
        ]);

        $logger->log($level, $message, $data);
    }
}

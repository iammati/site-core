<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility
{
    /**
     * Returns file reference(s) / object(s) for (multiple) file(s).
     *
     * @param int|string                                   $uid
     * @param string                                       $tableName
     * @param string                                       $fieldName
     * @param \TYPO3\CMS\Core\Resource\FileRepository|null $fileRepo
     *
     * @return void
     */
    public static function findFilesBy($uid, $tableName, $fieldName, $fileRepo = null)
    {
        if (is_null($fileRepo)) {
            // $fileRepo = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
            $fileRepo = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $fileRepo->findByRelation($tableName, $fieldName, $uid);
    }
}

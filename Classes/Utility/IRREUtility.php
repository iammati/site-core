<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IRREUtility
{
    /**
     * Returns Doctrine Statement which its result can call fetch or fetchAll method.
     *
     * @param int|string $parentid  the value used to compare using $fieldName-parameter
     * @param string     $tableName the table name for the where-condition
     * @param string     $fieldName the column name for the where-condition
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public static function findByUid($parentid, $tableName, $fieldName = 'parentid')
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $languageId = $context->getPropertyFromAspect('language', 'id');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        return $queryBuilder
            ->select('*')
            ->from($tableName)

            ->where(
                $queryBuilder->expr()->eq($fieldName, $parentid),
                $queryBuilder->expr()->eq('sys_language_uid', $languageId),
            )
            ->execute();
    }

    /**
     * Returns Doctrine Statement which its result can call fetch or fetchAll method.
     *
     * @param int|string $parentid   the value used to compare using $fieldName-parameter
     * @param string     $tableName  the table name for the where-condition
     * @param string     $fieldName  the column name for the where-condition
     * @param mixed      $repository
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public static function resolveByRepository($parentid, $tableName, $fieldName = 'parentid', $repository)
    {
        $pid = $GLOBALS['TSFE']->id;

        $context = GeneralUtility::makeInstance(Context::class);
        $languageId = $context->getPropertyFromAspect('language', 'id');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        $rows = $queryBuilder
            ->select('uid')
            ->from($tableName)

            ->where(
                $queryBuilder->expr()->eq($fieldName, $parentid),
                $queryBuilder->expr()->eq('pid', $pid),
                $queryBuilder->expr()->eq('sys_language_uid', $languageId),
            )
            ->execute()
            ->fetchAllAssociative();

        $models = [];

        foreach ($rows as $row) {
            $uid = $row['uid'];

            $models[] = $repository->findByUid($uid);
        }

        return $models;
    }
}

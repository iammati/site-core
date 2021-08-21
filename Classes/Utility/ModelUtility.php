<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModelUtility
{
    public static function updateSet($tableName, $uid, $column, $value, $queryBuilder = null)
    {
        if (is_null($queryBuilder)) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        }

        $queryBuilder
            ->update($tableName)

            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )

            ->set($column, $value)
            ->execute()
        ;

        return $value;
    }

    public static function updateGet($tableName, $uid, $column, $queryBuilder = null)
    {
        if (is_null($queryBuilder)) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        }

        $result = $queryBuilder
            ->select($column)
            ->from($tableName)

            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->execute()
            ->fetch()
        ;

        dd([$result, $column, $value]);
        if (is_array($result)) {
            return $result[$column];
        }

        return null;
    }
}

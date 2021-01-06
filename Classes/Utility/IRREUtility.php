<?php

declare(strict_types=1);

namespace Site\Core\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

class IRREUtility
{
    /**
     * Returns Doctrine Statement which its result can call fetch or fetchAll method.
     *
     * @param int|string $parentid  The value used to compare using $fieldName-parameter.
     * @param string $tableName The table name for the where-condition.
     * @param string $fieldName The column name for the where-condition.
     * @param int|string $language_id The sys_language_uid value - by default reading it from $GLOBALS['TSFE']-array.
     * @param int|string $fallback_language_id The fallback uid of the sys_language in case GLOBALS-TSFE fails or isn't set since it didn't initialized yet.
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public static function findByUid($parentid, $tableName, $fieldName, $language_id, $fallback_language_id = 0)
    {
        if (!$language_id) {
            $language_id = $GLOBALS['TSFE']->sys_language_uid ?? $fallback_language_id;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        return $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->eq($fieldName, $parentid),
                $queryBuilder->expr()->eq('sys_language_uid', $language_id),
            )
        ->execute();
    }
}

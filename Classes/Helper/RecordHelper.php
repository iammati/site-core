<?php

declare(strict_types=1);

namespace Site\Core\Helper;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordHelper
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder = null;

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $expressions = '';

    /**
     * @param string $tableName the name of the targeted table
     */
    public function setQueryBuilder(string $tableName): QueryBuilder
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $this->tableName = $tableName;

        return $this->queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Adder for the expressions.
     *
     * @param string $expr Any kind of expression e.g. $this->queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($value))
     *
     * @return void
     */
    public function addExpression(string $expr)
    {
        if ($this->expressions != '') {
            $this->expressions .= ' AND '.$expr;
        } else {
            $this->expressions .= $expr;
        }
    }

    /**
     * @return string
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * Returns QueryBuilder instance to handle it on your own
     * using additional method-calls as in orderBy etc. doing an execute and
     * at the end a fetchAll or anything similiar.
     *
     * @param string $tableName The table name for the where-condition
     * @param string $fieldName The column name for the where-condition
     * @param mixed  $value     The value used for $fieldName
     * @param string $select    Comma-List of columns / fields
     */
    public static function findPrecise(string $tableName, string $fieldName, $value, string $select = '*'): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        return $queryBuilder
            ->select($select)
            ->from($tableName)
            ->where($queryBuilder->expr()->eq($fieldName, $value))
        ;
    }

    /**
     * Retrives by provided constraints exactly the data you need.
     */
    public function find(): QueryBuilder
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where($this->getExpressions())
        ;
    }
}

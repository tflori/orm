<?php

namespace ORM\QueryBuilder;

use ORM\EntityManager;

class Parenthesis implements ParenthesisInterface
{
    /** @var string[] */
    protected $where = [];

    /** @var callable */
    protected $onClose;

    /** @var EntityManager */
    protected $entityManager;

    /** @var string */
    protected $connection;

    /** @var EntityManager */
    public static $defaultEntityManager;

    /** @var string */
    public static $defaultConnection = 'default';

    public function __construct(
        callable $onClose,
        EntityManager $entityManager = null,
        $connection = null
    ) {
        $this->onClose       = $onClose;
        $this->entityManager = $entityManager;
        $this->connection    = $connection;
    }

    protected static function convertPlaceholders(
        $expression,
        array $args,
        EntityManager $entityManager = null,
        $connection = null
    ) {
        if (!$entityManager) {
            $entityManager = static::$defaultEntityManager;
        }

        if (!$connection) {
            $connection = static::$defaultConnection;
        }

        $parts = explode('?', $expression);
        $expression = '';
        while ($part = array_shift($parts)) {
            $expression .= $part;
            if (count($args)) {
                $expression .= $entityManager->convertValue(array_shift($args), $connection);
            } elseif (count($parts)) {
                $expression .= '?';
            }
        }

        return $expression;
    }

    protected function getWhereCondition($column, $operator = '', $value = '')
    {
        if (strpos($column, '?') !== false) {
            $expression = $column;
            $value      = $operator;
        } elseif (!$operator && !$value) {
            $expression = $column;
        } else {
            if (!$value) {
                $value = $operator;
                if (is_array($value)) {
                    $operator = 'IN';
                } else {
                    $operator = '=';
                }
            }

            $expression = $expression = $column . ' ' . $operator;

            if (in_array(strtoupper($operator), ['IN', 'NOT IN'], true) && is_array($value)) {
                $expression .= ' (?' . str_repeat(',?', count($value) - 1) . ')';
            } else {
                $expression .= ' ?';
            }
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        if (strpos($expression, '?') !== false) {
            $whereCondition = static::convertPlaceholders($expression, $value, $this->entityManager, $this->connection);
        } else {
            $whereCondition = $expression;
        }

        return $whereCondition;
    }

    /** {@inheritdoc} */
    public function where($column, $operator = '', $value = '')
    {
        return $this->andWhere($column, $operator, $value);
    }

    /** {@inheritdoc} */
    public function andWhere($column, $operator = '', $value = '')
    {
        $this->where[] = (!empty($this->where) ? 'AND ' : '') . $this->getWhereCondition($column, $operator, $value);

        return $this;
    }

    /** {@inheritdoc} */
    public function orWhere($column, $operator = '', $value = '')
    {
        $this->where[] = (!empty($this->where) ? 'OR ' : '') . $this->getWhereCondition($column, $operator, $value);

        return $this;
    }

    /** {@inheritdoc} */
    public function parenthesis()
    {
        return $this->andParenthesis();
    }

    /** {@inheritdoc} */
    public function andParenthesis()
    {
        $parenthesis = new Parenthesis(function (ParenthesisInterface $parenthesis) {
            $this->where[] = (!empty($this->where) ? 'AND ' : '') . $parenthesis->getParenthesis();
            return $this;
        }, $this->entityManager, $this->connection);

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function orParenthesis()
    {
        $parenthesis = new Parenthesis(function (ParenthesisInterface $parenthesis) {
            $this->where[] = (!empty($this->where) ? 'OR ' : '') . $parenthesis->getParenthesis();
            return $this;
        }, $this->entityManager, $this->connection);

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function close()
    {
        return call_user_func($this->onClose, $this);
    }

    /** {@inheritdoc} */
    public function getParenthesis()
    {
        return '(' . implode(' ', $this->where) . ')';
    }
}

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

    /** @var ParenthesisInterface */
    protected $parent;

    /** @var EntityManager */
    public static $defaultEntityManager;

    /** @var string */
    public static $defaultConnection = 'default';

    public function __construct(
        callable $onClose,
        ParenthesisInterface $parent,
        EntityManager $entityManager = null,
        $connection = null
    ) {
        $this->onClose       = $onClose;
        $this->parent        = $parent;
        $this->entityManager = $entityManager;
        $this->connection    = $connection;
    }

    public function getWhereCondition($column, $operator = '', $value = '')
    {
        return $this->parent->getWhereCondition($column, $operator, $value);
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
        }, $this, $this->entityManager, $this->connection);

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function orParenthesis()
    {
        $parenthesis = new Parenthesis(function (ParenthesisInterface $parenthesis) {
            $this->where[] = (!empty($this->where) ? 'OR ' : '') . $parenthesis->getParenthesis();

            return $this;
        }, $this, $this->entityManager, $this->connection);

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

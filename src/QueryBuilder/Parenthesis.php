<?php

namespace ORM\QueryBuilder;

use ORM\EntityManager;

/**
 * Class Parenthesis
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
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

    public function __construct(
        callable $onClose,
        ParenthesisInterface $parent
    ) {
        $this->onClose       = $onClose;
        $this->parent        = $parent;
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
        }, $this);

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function orParenthesis()
    {
        $parenthesis = new Parenthesis(function (ParenthesisInterface $parenthesis) {
            $this->where[] = (!empty($this->where) ? 'OR ' : '') . $parenthesis->getParenthesis();

            return $this;
        }, $this);

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

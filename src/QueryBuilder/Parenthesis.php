<?php

namespace ORM\QueryBuilder;

/**
 * Class Parenthesis
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Parenthesis implements ParenthesisInterface
{
    /** Where conditions get concatenated with space
     * @var string[] */
    protected $where = [];

    /** Callback to close the parenthesis
     * @var callable */
    protected $onClose;

    /** Parent parenthesis or query
     * @var ParenthesisInterface */
    protected $parent;

    /**
     * Constructor
     *
     * Create a parenthesis inside another parenthesis or a query.
     *
     * @param callable             $onClose Callable that gets executed when the parenthesis get closed
     * @param ParenthesisInterface $parent  Parent where createWhereCondition get executed
     */
    public function __construct(
        callable $onClose,
        ParenthesisInterface $parent
    ) {
        $this->onClose = $onClose;
        $this->parent  = $parent;
    }

    /**
     * Create the where condition
     *
     * Calls createWhereCondition() from parent if there is a parent.
     *
     * @param string $column   Column or expression with placeholders
     * @param string $operator Operator, value or array of values
     * @param string $value    Value (required when used with operator)
     * @return string
     * @internal
     */
    public function createWhereCondition($column, $operator = null, $value = null)
    {
        return $this->parent->createWhereCondition($column, $operator, $value);
    }

    /** {@inheritdoc} */
    public function where($column, $operator = null, $value = null)
    {
        return $this->andWhere($column, $operator, $value);
    }

    /** {@inheritdoc} */
    public function andWhere($column, $operator = null, $value = null)
    {
        $this->where[] = (!empty($this->where) ? 'AND ' : '') . $this->createWhereCondition($column, $operator, $value);

        return $this;
    }

    /** {@inheritdoc} */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->where[] = (!empty($this->where) ? 'OR ' : '') . $this->createWhereCondition($column, $operator, $value);

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
        $parenthesis = new Parenthesis(
            function (ParenthesisInterface $parenthesis) {
                $this->where[] = (!empty($this->where) ? 'AND ' : '') . $parenthesis->getExpression();

                return $this;
            },
            $this
        );

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function orParenthesis()
    {
        $parenthesis = new Parenthesis(
            function (ParenthesisInterface $parenthesis) {
                $this->where[] = (!empty($this->where) ? 'OR ' : '') . $parenthesis->getExpression();

                return $this;
            },
            $this
        );

        return $parenthesis;
    }

    /** {@inheritdoc} */
    public function close()
    {
        return call_user_func($this->onClose, $this);
    }

    /** {@inheritdoc} */
    public function getExpression()
    {
        return '(' . implode(' ', $this->where) . ')';
    }
}

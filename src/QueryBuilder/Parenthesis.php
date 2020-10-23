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
    use HasWhereConditions;

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

    /** {@inheritdoc} */
    public function parenthesis()
    {
        return $this->andParenthesis();
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function createWhereCondition($column, $operator = null, $value = null)
    {
        return $this->parent->createWhereCondition($column, $operator, $value);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function buildWhereInExpression($column, array $values, $inverse = false)
    {
        return $this->parent->buildWhereInExpression($column, $values, $inverse);
    }

    /** {@inheritdoc} */
    public function andParenthesis()
    {
        return new Parenthesis(
            function (ParenthesisInterface $parenthesis) {
                $this->where[] = $this->wherePrefix('AND') . $parenthesis->getExpression();

                return $this;
            },
            $this
        );
    }

    /** {@inheritdoc} */
    public function orParenthesis()
    {
        return new Parenthesis(
            function (ParenthesisInterface $parenthesis) {
                $this->where[] = $this->wherePrefix('OR') . $parenthesis->getExpression();

                return $this;
            },
            $this
        );
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

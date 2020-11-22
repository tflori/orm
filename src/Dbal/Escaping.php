<?php

namespace ORM\Dbal;

use DateTime;
use DateTimeZone;
use ORM\Exception\NotScalar;

trait Escaping
{
    /** @var string */
    protected $quotingCharacter = '"';
    /** @var string */
    protected $identifierDivider = '.';
    /** @var string */
    protected $booleanTrue = '1';
    /** @var string */
    protected $booleanFalse = '0';

    /**
     * Returns $identifier quoted for use in a sql statement
     *
     * @param string $identifier Identifier to quote
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        if ($identifier instanceof Expression) {
            return (string)$identifier;
        }

        $quote = $this->quotingCharacter;
        $divider = $this->identifierDivider;
        return $quote . str_replace($divider, $quote . $divider . $quote, $identifier) . $quote;
    }

    /**
     * Returns $value formatted to use in a sql statement.
     *
     * @param  mixed $value The variable that should be returned in SQL syntax
     * @return string
     * @throws NotScalar
     */
    public function escapeValue($value)
    {
        if ($value instanceof DateTime) {
            return $this->escapeDateTime($value);
        }

        if ($value instanceof Expression) {
            return (string)$value;
        }

        $type = is_object($value) ? get_class($value) : gettype($value);
        $method = [ $this, 'escape' . ucfirst($type) ];

        if (is_callable($method)) {
            return call_user_func($method, $value);
        } else {
            throw new NotScalar('$value has to be scalar data type. ' . gettype($value) . ' given');
        }
    }

    /**
     * Escape a string for query
     *
     * @param string $value
     * @return string
     */
    protected function escapeString($value)
    {
        return $this->entityManager->getConnection()->quote($value);
    }

    /**
     * Escape an integer for query
     *
     * @param int $value
     * @return string
     */
    protected function escapeInteger($value)
    {
        return (string) $value;
    }

    /**
     * Escape a double for Query
     *
     * @param double $value
     * @return string
     */
    protected function escapeDouble($value)
    {
        return (string) $value;
    }

    /**
     * Escape NULL for query
     *
     * @return string
     */
    protected function escapeNULL()
    {
        return 'NULL';
    }

    /**
     * Escape a boolean for query
     *
     * @param bool $value
     * @return string
     */
    protected function escapeBoolean($value)
    {
        return ($value) ? $this->booleanTrue : $this->booleanFalse;
    }

    /**
     * Escape a date time object for query
     *
     * @param DateTime $value
     * @return mixed
     */
    protected function escapeDateTime(DateTime $value)
    {
        $value->setTimezone(new DateTimeZone('UTC'));
        return $this->escapeString($value->format('Y-m-d\TH:i:s.u\Z'));
    }
}

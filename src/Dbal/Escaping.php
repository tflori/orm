<?php

namespace ORM\Dbal;

use DateTime;
use DateTimeZone;

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
     * Extract content from parenthesis in $type
     *
     * @param string $type
     * @return string
     */
    protected function extractParenthesis($type)
    {
        if (preg_match('/\((.+)\)/', $type, $match)) {
            return $match[1];
        }

        return null;
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

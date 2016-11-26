<?php

namespace ORM;

/**
 * Describes as Database configuration
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class DbConfig
{
    /** @var string */
    public $type;
    /** @var string */
    public $name;

    /** @var string */
    public $host = 'localhost';
    /** @var string  */
    public $port;

    /** @var string */
    public $user = 'root';
    /** @var string */
    public $pass;

    /** @var array */
    public $attributes = [];

    /**
     * Database constructor.
     *
     * Attributes is an array of attributes to be set. Example:
     * $attributes = [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];
     *
     * @param string $type
     * @param string $name
     * @param string $user
     * @param string $pass
     * @param string $host
     * @param string $port
     * @param array  $attributes
     */
    public function __construct(
        $type,
        $name,
        $user = null,
        $pass = null,
        $host = null,
        $port = null,
        $attributes = []
    ) {

        $this->type = $type;
        $this->name = $name;

        $this->user = $user ?: $this->user;
        $this->pass = $pass;

        $this->host = $host ?: $this->host;

        $this->attributes = $attributes;

        switch ($type) {
            case 'mysql':
                $this->port = $port ?: '3306';
                break;

            case 'pgsql':
                $this->port = $port ?: '5432';
                break;
        }
    }

    public function getDsn()
    {
        $dsn = $this->type . ':';

        switch ($this->type) {
            case 'mysql':
                if ($this->host[0] === '/') {
                    $dsn .= 'unix_socket=' . $this->host;
                } else {
                    $dsn .= 'host=' . $this->host . ';port=' . $this->port;
                }

                $dsn .= ';dbname=' . $this->name;
                break;

            case 'pgsql':
                $dsn .= 'host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->name;
                break;

            case 'sqlite':
                $dsn .= $this->name;
                break;
        }

        return $dsn;
    }
}

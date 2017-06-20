<?php

namespace ORM;

/**
 * Describes a database configuration
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class DbConfig
{
    /** Dabase Type (mysql, pgsql or sqlite)
     * @var string */
    public $type;

    /** Database name or path for sqlite
     * @var string */
    public $name;

    /** Hostname or ip address
     * @var string */
    public $host = 'localhost';

    /** Port for DBMS (defaults to 3306 for mysql and 5432 for pgsql)
     * @var string  */
    public $port;

    /** Database user
     * @var string */
    public $user = 'root';

    /** Database password
     * @var string */
    public $pass;

    /** PDO attributes
     * @var array */
    public $attributes = [];

    /**
     * Constructor
     *
     * The constructor gets all parameters to establish a database connection and configure PDO instance.
     *
     * Example:
     *
     * ```php?start_inline=true
     * $dbConfig = new DbConfig('mysql', 'my_db', 'my_user', 'my_secret', null, null, [
     *     \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
     * ]);
     * ```
     *
     * @param string $type       Type of database (currently supported: `mysql`, `pgsql` and `sqlite`)
     * @param string $name       The name of the database or the path for sqlite
     * @param string $user       Username to use for connection
     * @param string $pass       Password
     * @param string $host       Hostname or IP address - defaults to `localhost`
     * @param string $port       Port - default ports (mysql: 3306, pgsql: 5432)
     * @param array  $attributes Array of PDO attributes
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
                isset($this->attributes[\PDO::ATTR_EMULATE_PREPARES])
                    || $this->attributes[\PDO::ATTR_EMULATE_PREPARES] = false;
                isset($this->attributes[\PDO::MYSQL_ATTR_INIT_COMMAND])
                    || $this->attributes[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET sql_mode ='ANSI_QUOTES', NAMES utf8";
                break;

            case 'pgsql':
                $this->port = $port ?: '5432';
                break;

            default:
                $this->port = $port;
        }
    }

    /**
     * Get the data source name
     *
     * @return string
     */
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

            case 'sqlite':
                $dsn .= $this->name;
                break;

            case 'pgsql':
            default:
                $dsn .= 'host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->name;
                break;
        }

        return $dsn;
    }
}

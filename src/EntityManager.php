<?php

namespace ORM;

use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\NoConnection;

class EntityManager
{
    const OPT_DEFAULT_CONNECTION = 'connection';
    const OPT_CONNECTIONS        = 'connections';

    /**@var \PDO[]|callable[]|DbConfig[] */
    protected $connections = [];

    /**
     * EntityManager constructor.
     * @param array $options
     * @throws InvalidConfiguration
     */
    public function __construct($options = [])
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case self::OPT_DEFAULT_CONNECTION:
                    $this->setConnection('default', $value);
                    break;
                case self::OPT_CONNECTIONS:
                    if (!is_array($value)) {
                        throw new InvalidConfiguration('OPT_CONNECTIONS requires an array');
                    }
                    foreach ($value as $name => $connection) {
                        $this->setConnection($name, $connection);
                    }
                    break;
            }
        }
    }

    public function setConnection($name, $connection)
    {
        if (is_callable($connection) || $connection instanceof DbConfig) {
            $this->connections[$name] = $connection;
        } elseif (is_array($connection)) {
            $dbConfigReflection = new \ReflectionClass(DbConfig::class);
            $this->connections[$name] = $dbConfigReflection->newInstanceArgs($connection);
        } else {
            throw new InvalidConfiguration(
                'Connection must be callable, DbConfig or an array of parameters for DbConfig::__constructor'
            );
        }
    }

    /**
     * @param string $name
     * @return \PDO
     * @throws NoConnection
     */
    public function getConnection($name = 'default')
    {
        if (!isset($this->connections[$name])) {
            throw new NoConnection('Unknown database connection ' . $name);
        }

        if (!$this->connections[$name] instanceof \PDO) {
            if ($this->connections[$name] instanceof DbConfig) {
                /** @var DbConfig $dbConfig */
                $dbConfig = $this->connections[$name];
                $this->connections[$name] = $pdo = new \PDO(
                    $dbConfig->getDsn(),
                    $dbConfig->user,
                    $dbConfig->pass
                );

                foreach ($dbConfig->attributes as $attribute => $value) {
                    $pdo->setAttribute($attribute, $value);
                }
            } else {
                $pdo = call_user_func($this->connections[$name]);
                if (!$pdo instanceof \PDO) {
                    throw new NoConnection('Getter for ' . $name . ' does not return PDO instance');
                }
                $this->connections[$name] = $pdo;
            }
        }

        return $this->connections[$name];
    }
}

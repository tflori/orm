<?php

namespace ORM;

use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidName;

/**
 * Abstract class of entity.
 *
 * The instance of an entity represents a row of the table.
 *
 * The class and statics describe the table.
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
abstract class Entity
{
    /** @var string */
    public static $tableNameTemplate = '%short%';

    /** @var string */
    public static $namingSchemeDb = 'snake_lower';

    /** @var string */
    protected static $tableName;

    /** @var string[] */
    protected static $tableNames = [];

    /** @var \ReflectionClass[] */
    protected static $reflections = [];

    /**
     * Get the table name.
     *
     * @return string
     * @throws InvalidName|InvalidConfiguration
     */
    public static function getTableName()
    {
        if (static::$tableName) {
            return static::$tableName;
        }

        if (!isset(self::$tableNames[static::class])) {
            $reflection = self::getReflection();

            $tableName = preg_replace_callback('/%([a-z]+)(\[(-?\d+)(\*)?\])?%/', function ($match) use ($reflection) {
                switch ($match[1]) {
                    case 'short':
                        $words = [$reflection->getShortName()];
                        break;

                    case 'namespace':
                        $words = explode('\\', $reflection->getNamespaceName());
                        break;

                    case 'name':
                        $words = preg_split('/[\\\\_]+/', $reflection->getName());
                        break;

                    default:
                        throw new InvalidConfiguration(
                            'Template invalid: Placeholder %' . $match[1] . '% is not allowed'
                        );
                }

                if (!isset($match[2])) {
                    return implode('_', $words);
                }
                $from = $match[3][0] === '-' ? count($words) - substr($match[3], 1) : $match[3];
                if (isset($words[$from])) {
                    return !isset($match[4]) ?
                        $words[$from] :
                        implode('_', array_slice($words, $from));
                }
                return '';
            }, static::$tableNameTemplate);

            if (empty($tableName)) {
                throw new InvalidName('Table name can not be empty');
            }
            self::$tableNames[static::class] = self::forceNamingScheme($tableName, static::$namingSchemeDb);
        }

        return self::$tableNames[static::class];
    }

    /**
     * Enforces $namingScheme to $name.
     *
     * @param string $name
     * @param string $namingScheme
     * @return string
     * @throws InvalidConfiguration
     */
    protected static function forceNamingScheme($name, $namingScheme)
    {
        $words = explode('_', preg_replace(
            '/([a-z0-9])([A-Z])/',
            '$1_$2',
            preg_replace_callback('/([A-Z][A-Z]+)([A-Z][a-z])/', function ($d) {
                return '_' . $d[1] . '_' . $d[2];
            }, $name)
        ));

        switch ($namingScheme) {
            case 'snake_case':
                $newName = implode('_', $words);
                break;

            case 'snake_lower':
                $newName = implode('_', array_map('strtolower', $words));
                break;

            case 'SNAKE_UPPER':
                $newName = implode('_', array_map('strtoupper', $words));
                break;

            case 'Snake_Ucfirst':
                $newName = implode('_', array_map('ucfirst', $words));
                break;

            case 'camelCase':
                $newName = lcfirst(implode('', array_map('ucfirst', array_map('strtolower', $words))));
                break;

            case 'StudlyCaps':
                $newName = implode('', array_map('ucfirst', array_map('strtolower', $words)));
                break;

            case 'lower':
                $newName = implode('', array_map('strtolower', $words));
                break;

            case 'UPPER':
                $newName = implode('', array_map('strtoupper', $words));
                break;

            default:
                throw new InvalidConfiguration('Naming scheme ' . $namingScheme . ' unknown');
        }

        return $newName;
    }

    /**
     * Get reflection of the entity class.
     *
     * @return \ReflectionClass
     */
    protected static function getReflection()
    {
        if (!isset(self::$reflections[static::class])) {
            self::$reflections[static::class] = new \ReflectionClass(static::class);
        }
        return self::$reflections[static::class];
    }
}

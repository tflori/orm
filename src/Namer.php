<?php

namespace ORM;

use ORM\Exceptions\InvalidConfiguration;
use ReflectionClass;

class Namer
{
    /** The template to use to calculate the table name.
     * @var string */
    protected $tableNameTemplate = '%short%';

    /** The naming scheme to use for table names.
     * @var string */
    protected $tableNameScheme = 'snake_lower';

    /** The naming scheme to use for column names.
     * @var string */
    protected $columnNameScheme = 'snake_lower';

    /** The naming scheme used for method names.
     * @var string */
    protected $methodNameScheme = 'camelCase';

    public function __construct($options = [])
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    public function setOption($option, $value)
    {
        switch ($option) {
            case EntityManager::OPT_TABLE_NAME_TEMPLATE:
                $this->tableNameTemplate = $value;
                break;

            case EntityManager::OPT_NAMING_SCHEME_TABLE:
                $this->tableNameScheme = $value;
                break;

            case EntityManager::OPT_NAMING_SCHEME_COLUMN:
                $this->columnNameScheme = $value;
                break;

            case EntityManager::OPT_NAMING_SCHEME_METHODS:
                $this->methodNameScheme = $value;
                break;
        }
    }

    public function getTableName(ReflectionClass $reflection, $template = null, $namingScheme = null)
    {
        if ($template === null) {
            $template = $this->tableNameTemplate;
        }

        if ($namingScheme === null) {
            $namingScheme = $this->tableNameScheme;
        }

        $name = $this->substitute($template, [
            'short' => $reflection->getShortName(),
            'namespace' => explode('\\', $reflection->getNamespaceName()),
            'name' => preg_split('/[\\\\_]+/', $reflection->getName()),
        ]);

        return $this->forceNamingScheme($name, $namingScheme);
    }

    /**
     * Substitute a $template with $values
     *
     * $values is a key value pair array. The value should be a string or an array o
     *
     * @param string $template
     * @param array  $values
     * @param string $arrayGlue
     * @return string
     */
    public function substitute($template, $values = [], $arrayGlue = '_')
    {
        return preg_replace_callback(
            '/%(.*?)%/',
            function ($match) use ($values, $arrayGlue) {
                // escape % with another % ( %% => % )
                if ($match[0] === '%%') {
                    return '%';
                }

                $var = trim($match[1]);

                if (preg_match('/\[(-?\d+\*?)\]$/', $var, $arrayAccessor)) {
                    $var = substr($var, 0, strpos($var, '['));
                    $arrayAccessor = $arrayAccessor[1];
                }

                // throw when the variable is unknown
                if (!array_key_exists($var, $values)) {
                    throw new InvalidConfiguration(
                        'Template invalid: Placeholder %' . $match[1] . '% is not allowed'
                    );
                }

                if (is_scalar($values[$var]) || is_null($values[$var])) {
                    return (string)$values[$var];
                }

                // otherwise we assume it is an array
                $array = $values[$var];

                if (isset($arrayAccessor[0])) {
                    $from = $arrayAccessor[0] === '-' ?
                        count($array) - abs($arrayAccessor) : (int)$arrayAccessor;

                    if ($from >= count($array)) {
                        return '';
                    }

                    $array = substr($arrayAccessor, -1) === '*' ?
                        array_slice($array, $from) : [$array[$from]];
                }

                return implode($arrayGlue, $array);
            },
            $template
        );
    }

    /**
     * Enforce $namingScheme to $name
     *
     * Supported naming schemes: snake_case, snake_lower, SNAKE_UPPER, Snake_Ucfirst, camelCase, StudlyCaps, lower
     * and UPPER.
     *
     * @param string $name         The name of the var / column
     * @param string $namingScheme The naming scheme to use
     * @return string
     * @throws InvalidConfiguration
     */
    protected function forceNamingScheme($name, $namingScheme)
    {
        $words = explode('_', preg_replace(
            '/([a-z0-9])([A-Z])/',
            '$1_$2',
            preg_replace_callback('/([a-z0-9])?([A-Z]+)([A-Z][a-z])/', function ($d) {
                return ($d[1] ? $d[1] . '_' : '') . $d[2] . '_' . $d[3];
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
}

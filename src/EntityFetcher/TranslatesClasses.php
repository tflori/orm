<?php

namespace ORM\EntityFetcher;

use ORM\Entity;
use ORM\Exception\NotJoined;

trait TranslatesClasses
{
    /** The class to alias mapping and vise versa
     * @var string[][] */
    protected $classMapping = [
        'byClass' => [],
        'byAlias' => [],
    ];

    /**
     * Translate attribute names in an expression to their column names
     *
     * @param string $expression
     * @return string
     * @throws NotJoined
     */
    protected function translateColumn($expression)
    {
        return preg_replace_callback(
            '/(?<b>^| |\()' .
            '((?<class>[A-Za-z_][A-Za-z0-9_\\\\]*)::|(?<alias>[A-Za-z_][A-Za-z0-9_]+)\.)?' .
            '(?<column>[A-Za-z_][A-Za-z0-9_]*)' .
            '(?<a>$| |,|\))/',
            function ($match) {
                if ($match['alias'] && !isset($this->classMapping['byAlias'][$match['alias']])) {
                    return $match[0];
                } elseif ($match['column'] === strtoupper($match['column'])) {
                    return $match['b'] . $match['column'] . $match['a'];
                }

                list($class, $alias) = $this->toClassAndAlias($match);

                /** @var Entity|string $class */
                return $match['b'] . $this->entityManager->escapeIdentifier(
                    $alias . '.' . $class::getColumnName($match['column'])
                ) . $match['a'];
            },
            $expression
        );
    }

    /**
     * Get class and alias by the match from translateColumn
     *
     * @param array $match
     * @return array [$class, $alias]
     * @throws NotJoined
     */
    private function toClassAndAlias(array $match)
    {
        if ($match['class']) {
            if (!isset($this->classMapping['byClass'][$match['class']])) {
                throw new NotJoined("Class " . $match['class'] . " not joined");
            }
            $class = $match['class'];
            $alias = $this->classMapping['byClass'][$match['class']];
        } elseif ($match['alias']) {
            $alias = $match['alias'];
            $class = $this->classMapping['byAlias'][$match['alias']];
        } else {
            $class = $this->class;
            $alias = $this->alias;
        }

        return [$class, $alias];
    }

    /**
     * Get the table name and alias for a class
     *
     * @param string $class
     * @param string $alias
     * @return array [$table, $alias]
     */
    protected function getTableAndAlias($class, $alias = '')
    {
        if (class_exists($class)) {
            /** @var Entity|string $class */
            $table = $this->entityManager->escapeIdentifier($class::getTableName());
            $alias = $alias ?: 't' . count($this->classMapping['byAlias']);

            $this->classMapping['byClass'][$class] = $alias;
            $this->classMapping['byAlias'][$alias] = $class;
        } else {
            $table = $class;
        }

        return [$table, $alias];
    }
}

<?php

namespace ORM\Entity;

use ORM\EntityManager as EM;

trait Naming
{
    /** The template to use to calculate the table name.
     * @var string */
    protected static $tableNameTemplate;

    /** The naming scheme to use for table names.
     * @var string */
    protected static $namingSchemeTable;

    /** The naming scheme to use for column names.
     * @var string */
    protected static $namingSchemeColumn;

    /** The naming scheme to use for method names.
     * @var string */
    protected static $namingSchemeMethods;

    /** Fixed table name (ignore other settings)
     * @var string */
    protected static $tableName;

    /** The naming scheme to use for attributes.
     * @var string */
    protected static $namingSchemeAttributes;

    /** Fixed column names (ignore other settings)
     * @var string[] */
    protected static $columnAliases = [];

    /** A prefix for column names.
     * @var string */
    protected static $columnPrefix;

    /**
     * Get the column name of $attribute
     *
     * The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
     * to $namingSchemeColumn.
     *
     * **ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
     * the same as getColumnName($name).
     *
     * @param string $attribute
     * @return string
     */
    public static function getColumnName($attribute)
    {
        if (isset(static::$columnAliases[$attribute])) {
            return static::$columnAliases[$attribute];
        }

        return EM::getInstance(static::class)->getNamer()
            ->getColumnName(static::class, $attribute, static::$columnPrefix, static::$namingSchemeColumn);
    }

    /**
     * Get the column name of $attribute
     *
     * The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
     * to $namingSchemeColumn.
     *
     * **ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
     * the same as getColumnName($name).
     *
     * @param string $column
     * @return string
     */
    public static function getAttributeName($column)
    {
        $attributeName = array_search($column, static::$columnAliases);
        if ($attributeName !== false) {
            return $attributeName;
        }

        return EM::getInstance(static::class)->getNamer()
            ->getAttributeName($column, static::$columnPrefix, static::$namingSchemeAttributes);
    }

    /**
     * Get the table name
     *
     * The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
     * $tableName.
     *
     * @return string
     */
    public static function getTableName()
    {
        if (static::$tableName) {
            return static::$tableName;
        }

        return EM::getInstance(static::class)->getNamer()
            ->getTableName(static::class, static::$tableNameTemplate, static::$namingSchemeTable);
    }

    // DEPRECATED stuff

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getTableNameTemplate()
    {
        return static::$tableNameTemplate;
    }

    /**
     * @param string $tableNameTemplate
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setTableNameTemplate($tableNameTemplate)
    {
        static::$tableNameTemplate = $tableNameTemplate;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeTable()
    {
        return static::$namingSchemeTable;
    }

    /**
     * @param string $namingSchemeTable
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeTable($namingSchemeTable)
    {
        static::$namingSchemeTable = $namingSchemeTable;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeColumn()
    {
        return static::$namingSchemeColumn;
    }

    /**
     * @param string $namingSchemeColumn
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeColumn($namingSchemeColumn)
    {
        static::$namingSchemeColumn = $namingSchemeColumn;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeMethods()
    {
        return static::$namingSchemeMethods;
    }

    /**
     * @param string $namingSchemeMethods
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeMethods($namingSchemeMethods)
    {
        static::$namingSchemeMethods = $namingSchemeMethods;
    }
}

<?php

namespace ORM\Test\Entity\Examples;

class StaticTableName extends TestEntity
{
    protected static $tableName = 'my_table';
    protected static $primaryKey = ['table', 'name', 'foo'];
    protected static $columnPrefix = 'stn_';

    protected static $columnAliases = [
        'foo' => 'bar'
    ];
}

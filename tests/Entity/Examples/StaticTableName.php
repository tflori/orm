<?php

namespace ORM\Test\Entity\Examples;

use ORM\Test\TestEntity;

class StaticTableName extends TestEntity
{
    protected static $tableName = 'my_table';
    protected static $primaryKey = ['table', 'name', 'foo'];
    protected static $columnPrefix = 'stn_';

    protected static $columnAliases = [
        'foo' => 'bar'
    ];

    protected $data = [
        'bar' => 'default'
    ];
}

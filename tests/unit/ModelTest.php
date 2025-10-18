<?php

use system\core\Model;

class TestModel extends Model
{
    protected $table = 'test_table';

    public function getTable()
    {
        return $this->table;
    }

    public function getDb()
    {
        return $this->db;
    }
}

class ModelTest extends TestCase
{
    public function testModelIsAbstract()
    {
        $reflection = new ReflectionClass('system\core\Model');
        $this->assertTrue($reflection->isAbstract());
    }

    public function testModelCanBeExtended()
    {
        // Test basic instantiation without database connection
        $reflection = new ReflectionClass('TestModel');
        $this->assertTrue($reflection->isSubclassOf('system\core\Model'));
    }
}
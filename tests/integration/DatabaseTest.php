<?php

use system\database\Database;

class DatabaseTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip if SQLite not available
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('SQLite PDO extension not available');
        }

        // Create a fresh in-memory database for each test
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create test table
        $this->pdo->exec('
            CREATE TABLE test_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                email TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Create a mock database instance
        $this->db = new Database();
        $reflection = new ReflectionClass($this->db);
        $pdoProperty = $reflection->getProperty('_pdo');
        $pdoProperty->setAccessible(true);
        $pdoProperty->setValue($this->db, $this->pdo);

        // Set table name
        $tableProperty = $reflection->getProperty('_table');
        $tableProperty->setAccessible(true);
        $tableProperty->setValue($this->db, 'test_users');
    }


    public function testInsertAndSelect()
    {
        // Insert test data
        $result = $this->db->table('test_users')->insert([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertTrue($result);

        // Select and verify
        $users = $this->db->table('test_users')->select();
        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users[0]->name);
        $this->assertEquals('john@example.com', $users[0]->email);
    }

    public function testUpdate()
    {
        // Insert first
        $this->db->table('test_users')->insert([
            'name' => 'John',
            'email' => 'john@test.com'
        ]);

        // Update
        $result = $this->db->table('test_users')
                          ->where('name', 'John')
                          ->update(['name' => 'Jane']);

        $this->assertTrue($result);

        // Verify
        $user = $this->db->table('test_users')->first();
        $this->assertEquals('Jane', $user->name);
    }

    public function testDelete()
    {
        // Insert
        $this->db->table('test_users')->insert([
            'name' => 'Delete Me',
            'email' => 'delete@test.com'
        ]);

        // Delete
        $result = $this->db->table('test_users')
                          ->where('name', 'Delete Me')
                          ->delete();

        $this->assertTrue($result);

        // Verify
        $users = $this->db->table('test_users')->select();
        $this->assertCount(0, $users);
    }

    public function testWhereConditions()
    {
        // Insert multiple records
        $this->db->table('test_users')->insert(['name' => 'Alice', 'email' => 'alice@test.com']);
        $this->db->table('test_users')->insert(['name' => 'Bob', 'email' => 'bob@test.com']);
        $this->db->table('test_users')->insert(['name' => 'Charlie', 'email' => 'charlie@test.com']);

        // Test where
        $alice = $this->db->table('test_users')->where('name', 'Alice')->select();
        $this->assertCount(1, $alice);
        $this->assertEquals('alice@test.com', $alice[0]->email);

        // Test where with different operator
        $results = $this->db->table('test_users')->where('id', '>', 1)->select();
        $this->assertCount(2, $results);
    }

    public function testOrderByAndLimit()
    {
        // Insert records
        $this->db->table('test_users')->insert(['name' => 'Alice', 'email' => 'alice@test.com']);
        $this->db->table('test_users')->insert(['name' => 'Bob', 'email' => 'bob@test.com']);
        $this->db->table('test_users')->insert(['name' => 'Charlie', 'email' => 'charlie@test.com']);

        // Test order by
        $ordered = $this->db->table('test_users')
                           ->orderBy('name', 'DESC')
                           ->select();

        $this->assertEquals('Charlie', $ordered[0]->name);
        $this->assertEquals('Bob', $ordered[1]->name);
        $this->assertEquals('Alice', $ordered[2]->name);

        // Test limit
        $limited = $this->db->table('test_users')
                           ->orderBy('name')
                           ->limit(2)
                           ->select();

        $this->assertCount(2, $limited);
    }

    public function testQueryErrors()
    {
        // Test invalid query - should not throw exception but return false/null
        $result = $this->db->query('SELECT * FROM nonexistent_table');
        $this->assertFalse($result);
    }
}
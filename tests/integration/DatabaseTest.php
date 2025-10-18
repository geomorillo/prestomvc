<?php

use system\database\Database;

class DatabaseTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Use test database configuration
        $this->db = Database::connect();
        $this->setupTestTable();
    }

    private function setupTestTable()
    {
        try {
            // Create test table
            $this->db->query('DROP TABLE IF EXISTS test_users');
            $this->db->query('
                CREATE TABLE test_users (
                    id INTEGER PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100),
                    email VARCHAR(100),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ');
        } catch (Exception $e) {
            $this->markTestSkipped('Could not create test table: ' . $e->getMessage());
        }
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
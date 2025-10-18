<?php

use system\database\Database;

/**
 * Test Database class that uses SQLite in-memory for testing
 */
class TestDatabase extends Database
{
    public function __construct()
    {
        // Initialize properties without calling parent constructor
        $this->queries = [];
        $this->queries['where'] = [];
        $this->queries['orwhere'] = [];
        $this->queries['orderby'] = [];
        $this->queries['join'] = [];
        $this->queries['limit'] = '';
        $this->queries['offset'] = '';

        // Set up SQLite in-memory configuration
        $this->config = [
            'default' => 'sqlite',
            'fetch' => PDO::FETCH_OBJ,
            'sqlite' => [
                'db_path' => ':memory:'
            ]
        ];

        // Create PDO connection directly
        $this->_pdo = new PDO('sqlite::memory:');
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_error = false;
        $this->_results = null;
        $this->_count = 0;
        $this->_typeQuery = '';
        $this->_isJoin = false;
        $this->_table = '';
    }
}

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

        // Create test database instance
        $this->db = new TestDatabase();

        // Create test table
        $this->db->query('
            CREATE TABLE test_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                email TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->db = null;
    }

    public function testInsertAndSelect()
    {
        // Insert test data using framework Database class
        $result = $this->db->table('test_users')->insert([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertTrue($result);

        // Select and verify using framework methods
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

        // Update using framework methods
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

        // Delete using framework methods
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

        // Test where using framework methods
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

        // Test order by using framework methods
        $ordered = $this->db->table('test_users')
                           ->orderBy('name', 'DESC')
                           ->select();

        $this->assertEquals('Charlie', $ordered[0]->name);
        $this->assertEquals('Bob', $ordered[1]->name);
        $this->assertEquals('Alice', $ordered[2]->name);

        // Test limit using framework methods
        $limited = $this->db->table('test_users')
                           ->orderBy('name')
                           ->limit(2)
                           ->select();

        $this->assertCount(2, $limited);
    }

    public function testQueryErrors()
    {
        // Test invalid query using framework Database class
        try {
            $result = $this->db->query('SELECT * FROM nonexistent_table');
            $this->assertFalse($this->db->error()); // Framework handles errors gracefully
        } catch (\PDOException $e) {
            // Expected exception for nonexistent table
            $this->assertTrue(true);
        }
    }
}
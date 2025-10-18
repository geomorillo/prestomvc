<?php

use system\cache\FileCache;

class CacheTest extends TestCase
{
    private $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new FileCache(ROOT . CACHE_DEFAULT_DIR . DS . 'test_');
    }

    public function testSetAndGet()
    {
        $testData = ['name' => 'John', 'age' => 30];
        $this->cache->set('test_key', $testData, 60);

        $result = $this->cache->get('test_key');
        $this->assertEquals($testData, $result);
    }

    public function testExpiration()
    {
        $this->cache->set('expire_key', 'test_value', 1);
        $this->assertEquals('test_value', $this->cache->get('expire_key'));

        sleep(2); // Wait for expiration
        $this->assertFalse($this->cache->get('expire_key'));
    }

    public function testDelete()
    {
        $this->cache->set('delete_key', 'value');
        $this->assertTrue($this->cache->has('delete_key'));

        $this->cache->delete('delete_key');
        $this->assertFalse($this->cache->has('delete_key'));
    }

    public function testClear()
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');

        $this->cache->clear();

        $this->assertFalse($this->cache->has('key1'));
        $this->assertFalse($this->cache->has('key2'));
    }

    public function testHas()
    {
        $this->assertFalse($this->cache->has('nonexistent'));

        $this->cache->set('existing', 'value');
        $this->assertTrue($this->cache->has('existing'));
    }

    public function testComplexData()
    {
        $complexData = [
            'users' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob']
            ],
            'metadata' => [
                'total' => 2,
                'page' => 1
            ]
        ];

        $this->cache->set('complex', $complexData);
        $result = $this->cache->get('complex');

        $this->assertEquals($complexData, $result);
        $this->assertArrayHasKeys(['users', 'metadata'], $result);
    }
}
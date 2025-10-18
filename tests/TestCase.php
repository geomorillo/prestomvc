<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any global state
        if (isset($_SESSION)) {
            $_SESSION = [];
        }

        // Reset registry
        if (class_exists('\system\core\Register')) {
            \system\core\Register::clear();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up after each test
        $this->cleanTestFiles();
    }

    protected function cleanTestFiles()
    {
        // Clean test cache files
        $cacheDir = ROOT . CACHE_DEFAULT_DIR . DS;
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . 'test_*' . CACHE_FILE_EXTENSION);
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    protected function createMockRequest($method = 'GET', $uri = '/', $params = [])
    {
        $mock = $this->createMock(\system\http\Request::class);
        $mock->method('getMethod')->willReturn($method);
        $mock->method('getUrl')->willReturn($uri);
        $mock->method('getParams')->willReturn($params);
        return $mock;
    }

    protected function assertArrayHasKeys(array $keys, array $array, string $message = '')
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array, $message ?: "Array should have key '$key'");
        }
    }

    protected function assertIsValidEmail($email)
    {
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            $email,
            'Email should be valid format'
        );
    }
}
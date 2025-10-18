<?php

use system\core\Logger;

class LoggerTest extends TestCase
{
    private $logFile;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a temporary log file for testing
        $this->logFile = sys_get_temp_dir() . DS . 'test_log_' . time() . '.log';
        // Override the static file property for testing
        $reflection = new ReflectionClass('system\core\Logger');
        $fileProperty = $reflection->getProperty('file');
        $fileProperty->setAccessible(true);
        $fileProperty->setValue(null, $this->logFile);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up test log file
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testLoggerImplementsLoggerInterface()
    {
        $reflection = new ReflectionClass('system\core\Logger');
        $this->assertTrue($reflection->implementsInterface('system\core\LoggerInterface'));
    }

    public function testEmergencyLog()
    {
        Logger::emergency('Emergency message');
        $this->assertLogContains('EMERGENCY: Emergency message');
    }

    public function testCriticalLog()
    {
        Logger::critical('Critical message');
        $this->assertLogContains('CRITICAL: Critical message');
    }

    public function testErrorLog()
    {
        Logger::error('Error message');
        $this->assertLogContains('ERROR: Error message');
    }

    public function testWarningLog()
    {
        Logger::warning('Warning message');
        $this->assertLogContains('WARNING: Warning message');
    }

    public function testInfoLog()
    {
        Logger::info('Info message');
        $this->assertLogContains('INFO: Info message');
    }

    public function testNoticeLog()
    {
        Logger::notice('Notice message');
        $this->assertLogContains('NOTICE: Notice message');
    }

    public function testAlertLog()
    {
        Logger::alert('Alert message');
        $this->assertLogContains('ALERT: Alert message');
    }

    public function testDebugLog()
    {
        Logger::debug('Debug message');
        $this->assertLogContains('DEBUG: Debug message');
    }

    public function testLogWithCustomLevel()
    {
        Logger::log('CUSTOM', 'Custom message');
        $this->assertLogContains('LOG LEVEL CUSTOM: Custom message');
    }

    public function testLogWithContext()
    {
        Logger::info('User {user} logged in from {ip}', ['user' => 'john', 'ip' => '192.168.1.1']);
        $this->assertLogContains('User john logged in from 192.168.1.1');
    }

    public function testLogWithEmptyContext()
    {
        Logger::info('Simple message', []);
        $this->assertLogContains('Simple message');
    }

    public function testLogWithNullContext()
    {
        Logger::info('Simple message', null);
        $this->assertLogContains('Simple message');
    }

    public function testMultipleLogEntries()
    {
        Logger::info('First message');
        Logger::error('Second message');
        Logger::warning('Third message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContains('First message', $content);
        $this->assertStringContains('Second message', $content);
        $this->assertStringContains('Third message', $content);
    }

    public function testLogIncludesTimestamp()
    {
        Logger::info('Test message');
        $content = file_get_contents($this->logFile);

        // Check that the log contains a date format like "January 1, 2024, 12:00 am"
        $this->assertMatchesRegularExpression('/\w+ \d+, \d{4}, \d{1,2}:\d{2} (am|pm)/', $content);
    }

    public function testLogContextWithNonStringValues()
    {
        // Objects and arrays should be ignored in interpolation
        Logger::info('User {user} has {items} items', [
            'user' => 'john',
            'items' => ['item1', 'item2'], // array should be ignored
            'count' => 5 // integer should be converted to string
        ]);

        $this->assertLogContains('User john has {items} items'); // array placeholder not replaced
    }

    private function assertLogContains($expectedContent)
    {
        $content = file_get_contents($this->logFile);
        $this->assertStringContains($expectedContent, $content);
    }
}
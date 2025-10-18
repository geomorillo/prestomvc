<?php

use system\core\Csrf;

class CsrfTest extends TestCase
{
    private $originalUseSessions;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock sessions for testing
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    public function testGenerateToken()
    {
        $token = Csrf::generate();
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        $this->assertEquals(44, strlen($token)); // base64 encoded 32 bytes
    }

    public function testValidateToken()
    {
        $token = Csrf::generate();
        $this->assertTrue(Csrf::validate($token));
    }


    public function testInvalidToken()
    {
        Csrf::generate(); // Generate a valid token first
        $this->assertFalse(Csrf::validate('invalid_token_12345'));
    }

    public function testTokenStorage()
    {
        $token1 = Csrf::generate();
        $token2 = Csrf::generate();

        // Should be different tokens
        $this->assertNotEquals($token1, $token2);

        // Only the last token should be valid (overwrites previous)
        $this->assertFalse(Csrf::validate($token1)); // First token invalidated
        $this->assertTrue(Csrf::validate($token2));  // Last token valid
    }


    public function testSaveOperation()
    {
        Csrf::save('test_key', 'test_value');
        $this->assertEquals('test_value', $_SESSION['test_key']);
    }

    public function testGetKeyOperation()
    {
        $_SESSION['test_key'] = 'test_value';
        $this->assertEquals('test_value', Csrf::get_key('test_key'));
    }

    public function testDestroyOperation()
    {
        $_SESSION['test_key'] = 'test_value';
        Csrf::destroy('test_key');
        $this->assertFalse(Csrf::get_key('test_key'));
        $this->assertFalse(isset($_SESSION['test_key']));
    }

    public function testGetKeyReturnsFalseForNonexistentKey()
    {
        $this->assertFalse(Csrf::get_key('nonexistent_key'));
    }

    public function testSaveDoesNothingWhenSessionNotSet()
    {
        // Temporarily unset $_SESSION
        $originalSession = $_SESSION;
        unset($_SESSION);

        Csrf::save('test_key', 'test_value');
        $this->assertFalse(isset($_SESSION));

        // Restore session
        $_SESSION = $originalSession;
    }
}
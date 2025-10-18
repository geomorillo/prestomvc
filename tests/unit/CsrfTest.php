<?php

use system\core\Csrf;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure sessions are available for CSRF
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    public function testGenerateToken()
    {
        $token = Csrf::generate();
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes * 2 for hex
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

        // Both should be valid
        $this->assertTrue(Csrf::validate($token1));
        $this->assertTrue(Csrf::validate($token2));
    }

    public function testTokenAfterValidation()
    {
        $token = Csrf::generate();
        $this->assertTrue(Csrf::validate($token));

        // Token should be invalidated after use
        $this->assertFalse(Csrf::validate($token));
    }

    public function testNoSessionAvailable()
    {
        // Temporarily disable sessions
        $originalSessions = USE_SESSIONS;
        if (defined('USE_SESSIONS')) {
            // This would require changing the constant, but for test we can mock
            $this->markTestSkipped('Cannot test without session support in current setup');
        }
    }
}
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
        // Skip if sessions not available
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions not enabled for testing');
        }

        $token = Csrf::generate();
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes * 2 for hex
    }

    public function testValidateToken()
    {
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions not enabled for testing');
        }

        $token = Csrf::generate();
        $this->assertTrue(Csrf::validate($token));
    }

    public function testInvalidToken()
    {
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions not enabled for testing');
        }

        Csrf::generate(); // Generate a valid token first
        $this->assertFalse(Csrf::validate('invalid_token_12345'));
    }

    public function testTokenStorage()
    {
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions not enabled for testing');
        }

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
        if (!USE_SESSIONS) {
            $this->markTestSkipped('Sessions not enabled for testing');
        }

        $token = Csrf::generate();
        $this->assertTrue(Csrf::validate($token));

        // Token should be invalidated after use
        $this->assertFalse(Csrf::validate($token));
    }
}
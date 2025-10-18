<?php

use system\helpers\ValidationHelper;

class ValidationTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValidationHelper([
            'login_username_empty' => 'Username is required',
            'login_username_long' => 'Username is too long',
            'register_password_nomatch' => 'Passwords do not match',
            'register_password_username' => 'Password cannot contain username'
        ]);
    }

    public function testValidUsername()
    {
        $this->assertTrue($this->validator->validateUsername('validuser'));
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testInvalidUsername()
    {
        // Empty username
        $this->assertFalse($this->validator->validateUsername(''));
        $this->assertTrue($this->validator->hasErrors());

        // Too long username
        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validateUsername(str_repeat('a', 31)));
        $this->assertTrue($this->validator->hasErrors());

        // Too short username
        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validateUsername('ab'));
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testPasswordValidation()
    {
        $this->assertTrue($this->validator->validatePassword('validpass123'));
        $this->assertFalse($this->validator->hasErrors());

        // Too short - should fail (MIN_PASSWORD_LENGTH = 5)
        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validatePassword('1234')); // 4 chars, too short
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testEmailValidation()
    {
        $this->assertTrue($this->validator->validateEmail('test@example.com'));
        $this->assertFalse($this->validator->hasErrors());

        // Invalid email
        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validateEmail('invalid-email'));
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testPasswordMatch()
    {
        $this->assertTrue($this->validator->validatePasswordMatch('pass123', 'pass123'));
        $this->assertFalse($this->validator->hasErrors());

        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validatePasswordMatch('pass123', 'pass456'));
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testPasswordNotUsername()
    {
        $this->assertTrue($this->validator->validatePasswordNotUsername('mypass123', 'myuser'));
        $this->assertFalse($this->validator->hasErrors());

        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validatePasswordNotUsername('myuser123', 'myuser'));
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testKeyValidation()
    {
        $validKey = str_repeat('a', RANDOM_KEY_LENGTH);
        $this->assertTrue($this->validator->validateKey($validKey));
        $this->assertFalse($this->validator->hasErrors());

        // Too short
        $this->validator->clearErrors();
        $this->assertFalse($this->validator->validateKey('short'));
        $this->assertTrue($this->validator->hasErrors());
    }

    public function testErrorCollection()
    {
        $this->validator->validateUsername('');
        $this->validator->validatePassword('1234'); // Too short

        $errors = $this->validator->getErrors();
        $this->assertCount(2, $errors);
        $this->assertContains('Username is required', $errors);
        $this->assertContains('Password is too short', $errors);
    }

    public function testErrorManagement()
    {
        $this->validator->validateUsername('');
        $this->assertTrue($this->validator->hasErrors());
        $this->assertEquals(1, $this->validator->getErrorCount());

        $this->validator->clearErrors();
        $this->assertFalse($this->validator->hasErrors());
        $this->assertEquals(0, $this->validator->getErrorCount());
    }
}
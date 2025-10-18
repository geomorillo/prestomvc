<?php

namespace system\helpers;

/**
 * Validation Helper - Centralized validation logic to eliminate code duplication
 * Provides reusable validation methods for common form validations
 */
class ValidationHelper
{
    private $errors = [];
    private $lang;

    public function __construct($lang = null)
    {
        $this->lang = $lang;
    }

    /**
     * Set language array for error messages
     */
    public function setLanguage($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Validate username field
     * @param string $username
     * @param string $prefix Language prefix (login, register, changepass, etc.)
     * @return bool
     */
    public function validateUsername($username, $prefix = 'register')
    {
        if (strlen($username) == 0) {
            $this->errors[] = $this->lang[$prefix . '_username_empty'] ?? 'Username is required';
            return false;
        }

        if (strlen($username) > MAX_USERNAME_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_username_long'] ?? 'Username is too long';
            return false;
        }

        if (strlen($username) < MIN_USERNAME_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_username_short'] ?? 'Username is too short';
            return false;
        }

        return true;
    }

    /**
     * Validate password field
     * @param string $password
     * @param string $prefix Language prefix
     * @return bool
     */
    public function validatePassword($password, $prefix = 'register')
    {
        if (strlen($password) == 0) {
            $this->errors[] = $this->lang[$prefix . '_password_empty'] ?? 'Password is required';
            return false;
        }

        if (strlen($password) > MAX_PASSWORD_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_password_long'] ?? 'Password is too long';
            return false;
        }

        if (strlen($password) < MIN_PASSWORD_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_password_short'] ?? 'Password is too short';
            return false;
        }

        return true;
    }

    /**
     * Validate email field
     * @param string $email
     * @param string $prefix Language prefix
     * @return bool
     */
    public function validateEmail($email, $prefix = 'register')
    {
        if (strlen($email) == 0) {
            $this->errors[] = $this->lang[$prefix . '_email_empty'] ?? 'Email is required';
            return false;
        }

        if (strlen($email) > MAX_EMAIL_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_email_long'] ?? 'Email is too long';
            return false;
        }

        if (strlen($email) < MIN_EMAIL_LENGTH) {
            $this->errors[] = $this->lang[$prefix . '_email_short'] ?? 'Email is too short';
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = $this->lang[$prefix . '_email_invalid'] ?? 'Email is invalid';
            return false;
        }

        return true;
    }

    /**
     * Validate that two passwords match
     * @param string $password1
     * @param string $password2
     * @return bool
     */
    public function validatePasswordMatch($password1, $password2)
    {
        if ($password1 !== $password2) {
            $this->errors[] = $this->lang['register_password_nomatch'] ?? 'Passwords do not match';
            return false;
        }
        return true;
    }

    /**
     * Validate that password does not contain username
     * @param string $password
     * @param string $username
     * @return bool
     */
    public function validatePasswordNotUsername($password, $username)
    {
        if (strstr($password, $username)) {
            $this->errors[] = $this->lang['register_password_username'] ?? 'Password cannot contain username';
            return false;
        }
        return true;
    }

    /**
     * Validate key field (for reset keys, activation keys, etc.)
     * @param string $key
     * @param string $prefix Language prefix
     * @param int $expectedLength Expected key length
     * @return bool
     */
    public function validateKey($key, $prefix = 'resetpass', $expectedLength = RANDOM_KEY_LENGTH)
    {
        if (strlen($key) == 0) {
            $this->errors[] = $this->lang[$prefix . '_key_empty'] ?? 'Key is required';
            return false;
        }

        if (strlen($key) < $expectedLength) {
            $this->errors[] = $this->lang[$prefix . '_key_short'] ?? 'Key is too short';
            return false;
        }

        if (strlen($key) > $expectedLength) {
            $this->errors[] = $this->lang[$prefix . '_key_long'] ?? 'Key is too long';
            return false;
        }

        return true;
    }

    /**
     * Get all validation errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if there are any validation errors
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Clear all validation errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Get first error message
     * @return string|null
     */
    public function getFirstError()
    {
        return $this->errors[0] ?? null;
    }

    /**
     * Get error count
     * @return int
     */
    public function getErrorCount()
    {
        return count($this->errors);
    }
}
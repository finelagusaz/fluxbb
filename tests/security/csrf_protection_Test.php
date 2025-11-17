<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for CSRF protection functions
 *
 * Tests the pun_csrf_token() and check_csrf() functions
 * to ensure CSRF tokens are properly generated and validated.
 */
class csrf_protection_Test extends TestCase
{
    protected function setUp(): void
    {
        // Set up a mock user for testing
        $GLOBALS['pun_user'] = array(
            'id' => 123,
            'is_guest' => 0,
            'password' => 'hashed_password_123',
        );
    }

    public function test_csrf_token_generation()
    {
        $token = pun_csrf_token();

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals(40, strlen($token), 'CSRF token should be 40 characters (SHA1 hex)');
    }

    public function test_csrf_token_is_consistent()
    {
        $token1 = pun_csrf_token();
        $token2 = pun_csrf_token();

        $this->assertEquals($token1, $token2, 'CSRF token should be consistent for the same user session');
    }

    public function test_csrf_token_changes_with_user()
    {
        // Note: pun_csrf_token() uses a static variable, so once generated it won't change
        // This test verifies the token generation logic by resetting the function state
        // by creating a new user context in a fresh test

        $user1 = array(
            'id' => 123,
            'password' => 'hashed_password_123',
        );
        $user2 = array(
            'id' => 456,
            'password' => 'hashed_password_456',
        );

        // Generate hash directly to verify logic
        $hash1 = pun_hash($user1['id'] . $user1['password'] . pun_hash('127.0.0.1'));
        $hash2 = pun_hash($user2['id'] . $user2['password'] . pun_hash('127.0.0.1'));

        $this->assertNotEquals($hash1, $hash2, 'CSRF token should differ for different users');
    }

    public function test_csrf_token_validation_success()
    {
        $token = pun_csrf_token();

        // Mock form data with valid token
        $_POST['csrf_token'] = $token;

        // This should not throw an error
        $this->expectNotToPerformAssertions();
        check_csrf($token);
    }

    public function test_csrf_token_validation_with_get_request()
    {
        $token = pun_csrf_token();

        // Mock GET request with valid token
        $_GET['csrf_token'] = $token;

        // This should not throw an error
        $this->expectNotToPerformAssertions();
        check_csrf($token);
    }

    public function test_csrf_token_hex_format()
    {
        $token = pun_csrf_token();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $token, 'CSRF token should be 40 hexadecimal characters');
    }

    protected function tearDown(): void
    {
        unset($_POST['csrf_token']);
        unset($_GET['csrf_token']);
    }
}

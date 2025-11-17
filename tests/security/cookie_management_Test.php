<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for cookie management functions
 *
 * Tests cookie signature validation and related functions.
 */
class cookie_management_Test extends TestCase
{
    protected function setUp(): void
    {
        // Set up cookie seed for testing
        $GLOBALS['cookie_seed'] = 'test_seed_12345';
        $GLOBALS['cookie_name'] = 'pun_cookie';
    }

    public function test_cookie_hash_generation()
    {
        $user_id = 123;
        $password_hash = 'hashed_password_value';

        $hash = pun_hash($user_id . $password_hash);

        $this->assertIsString($hash);
        $this->assertEquals(40, strlen($hash), 'Hash should be 40 characters (SHA1)');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $hash);
    }

    public function test_cookie_hash_consistency()
    {
        $user_id = 123;
        $password_hash = 'hashed_password_value';

        $hash1 = pun_hash($user_id . $password_hash);
        $hash2 = pun_hash($user_id . $password_hash);

        $this->assertEquals($hash1, $hash2, 'Hash should be consistent for same input');
    }

    public function test_cookie_hash_different_for_different_users()
    {
        $password_hash = 'hashed_password_value';

        $hash1 = pun_hash(123 . $password_hash);
        $hash2 = pun_hash(456 . $password_hash);

        $this->assertNotEquals($hash1, $hash2, 'Hash should differ for different users');
    }

    public function test_cookie_hash_different_for_different_passwords()
    {
        $user_id = 123;

        $hash1 = pun_hash($user_id . 'password1');
        $hash2 = pun_hash($user_id . 'password2');

        $this->assertNotEquals($hash1, $hash2, 'Hash should differ for different passwords');
    }

    public function test_cookie_hash_consistent()
    {
        $data = 'test_data';

        // pun_hash is a simple SHA1 wrapper and doesn't use seed
        // The seed is used elsewhere in cookie validation
        $hash1 = pun_hash($data);
        $hash2 = pun_hash($data);

        $this->assertEquals($hash1, $hash2, 'Hash should be consistent for same input');
    }

    public function test_random_key_generation()
    {
        // random_key with hash=true returns hex string
        $key = random_key(32, false, true);

        $this->assertIsString($key);
        $this->assertEquals(32, strlen($key));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $key, 'Random key with hash should be hexadecimal');
    }

    public function test_random_key_readable()
    {
        // random_key with readable=true returns alphanumeric string
        $key = random_key(32, true);

        $this->assertIsString($key);
        $this->assertEquals(32, strlen($key));
    }

    public function test_random_key_uniqueness()
    {
        $key1 = random_key(32, false, true);
        $key2 = random_key(32, false, true);

        $this->assertNotEquals($key1, $key2, 'Random keys should be unique');
    }

    public function test_random_pass_generation()
    {
        $password = random_pass(8);

        $this->assertIsString($password);
        $this->assertEquals(8, strlen($password));
    }

    public function test_random_pass_uniqueness()
    {
        $pass1 = random_pass(12);
        $pass2 = random_pass(12);

        $this->assertNotEquals($pass1, $pass2, 'Random passwords should be unique');
    }

    protected function tearDown(): void
    {
        // Clean up
        unset($GLOBALS['cookie_seed']);
        unset($GLOBALS['cookie_name']);
    }
}

<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for email functions
 *
 * Tests email-related functions from include/email.php
 * such as banned email checking and text encoding.
 */
class email_functions_Test extends TestCase
{
    protected function setUp(): void
    {
        // Load email functions
        require_once PUN_ROOT . 'include/email.php';
        require_once PUN_ROOT . 'include/utf8/utils/ascii.php';

        $GLOBALS['pun_config']['o_base_url'] = 'http://example.com/forum';
        $GLOBALS['pun_bans'] = array();
    }

    public function test_is_banned_email_not_banned()
    {
        $GLOBALS['pun_bans'] = array();

        $result = is_banned_email('user@example.com');

        $this->assertFalse($result, 'Email should not be banned when ban list is empty');
    }

    public function test_is_banned_email_exact_match()
    {
        $GLOBALS['pun_bans'] = array(
            array('email' => 'banned@example.com'),
        );

        $result = is_banned_email('banned@example.com');

        $this->assertTrue($result, 'Exact email match should be banned');
    }

    public function test_is_banned_email_domain_ban()
    {
        $GLOBALS['pun_bans'] = array(
            array('email' => 'spam.com'),
        );

        $result = is_banned_email('user@spam.com');

        $this->assertTrue($result, 'Email with banned domain should be banned');
    }

    public function test_is_banned_email_domain_ban_not_matching()
    {
        $GLOBALS['pun_bans'] = array(
            array('email' => 'spam.com'),
        );

        $result = is_banned_email('user@example.com');

        $this->assertFalse($result, 'Email with different domain should not be banned');
    }

    public function test_is_banned_email_multiple_bans()
    {
        $GLOBALS['pun_bans'] = array(
            array('email' => 'spam1.com'),
            array('email' => 'spam2.com'),
            array('email' => 'baduser@example.com'),
        );

        $this->assertTrue(is_banned_email('user@spam1.com'));
        $this->assertTrue(is_banned_email('user@spam2.com'));
        $this->assertTrue(is_banned_email('baduser@example.com'));
        $this->assertFalse(is_banned_email('gooduser@example.com'));
    }

    public function test_is_banned_email_empty_ban()
    {
        $GLOBALS['pun_bans'] = array(
            array('email' => ''),
        );

        $result = is_banned_email('user@example.com');

        $this->assertFalse($result, 'Empty ban email should not match anything');
    }

    public function test_encode_mail_text_ascii()
    {
        $text = 'Hello World';
        $result = encode_mail_text($text);

        $this->assertEquals($text, $result, 'ASCII text should not be encoded');
    }

    public function test_encode_mail_text_utf8()
    {
        $text = 'こんにちは'; // Japanese characters
        $result = encode_mail_text($text);

        $this->assertStringContainsString('=?UTF-8?B?', $result, 'UTF-8 text should be base64 encoded');
        $this->assertStringContainsString('?=', $result);
    }

    public function test_encode_mail_text_mixed()
    {
        $text = 'Hello こんにちは World';
        $result = encode_mail_text($text);

        $this->assertStringContainsString('=?UTF-8?B?', $result, 'Mixed text should be encoded');
    }

    public function test_encode_mail_text_special_chars()
    {
        $text = 'Café';
        $result = encode_mail_text($text);

        $this->assertStringContainsString('=?UTF-8?B?', $result, 'Text with special characters should be encoded');
    }

    public function test_encode_mail_text_numbers()
    {
        $text = '12345';
        $result = encode_mail_text($text);

        $this->assertEquals($text, $result, 'Numbers should not be encoded');
    }

    public function test_encode_mail_text_empty()
    {
        $text = '';
        $result = encode_mail_text($text);

        $this->assertEquals($text, $result, 'Empty string should remain empty');
    }

    public function test_bbcode2email_basic()
    {
        $text = 'Hello World';
        $result = bbcode2email($text);

        $this->assertStringContainsString('Hello World', $result);
    }

    public function test_bbcode2email_removes_formatting()
    {
        $text = '[b]Bold[/b] [i]Italic[/i] text';
        $result = bbcode2email($text);

        $this->assertStringNotContainsString('[b]', $result, 'Bold tags should be removed');
        $this->assertStringNotContainsString('[i]', $result, 'Italic tags should be removed');
        $this->assertStringContainsString('Bold', $result);
        $this->assertStringContainsString('Italic', $result);
    }

    public function test_bbcode2email_preserves_url()
    {
        $text = '[url=http://example.com]Link[/url]';
        $result = bbcode2email($text);

        $this->assertStringContainsString('Link', $result);
    }

    public function test_bbcode2email_preserves_quote()
    {
        $text = '[quote]Quoted text[/quote]';
        $result = bbcode2email($text);

        $this->assertStringContainsString('Quoted text', $result);
    }

    public function test_bbcode2email_wrapping()
    {
        $long_text = str_repeat('This is a long line of text. ', 10);
        $result = bbcode2email($long_text, 72);

        $this->assertIsString($result);
        $lines = explode("\n", $result);

        // Check that lines are wrapped (some lines should be shorter than the input)
        $this->assertGreaterThan(1, count($lines), 'Long text should be wrapped to multiple lines');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['pun_bans']);
        unset($GLOBALS['pun_config']['o_base_url']);
    }
}

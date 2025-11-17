<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for utility functions
 *
 * Tests various utility functions from include/functions.php
 * such as pagination, time formatting, string operations, etc.
 */
class utility_functions_Test extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['pun_config']['o_base_url'] = 'http://example.com/forum';
        $GLOBALS['pun_user'] = array(
            'timezone' => 0,
            'dst' => 0,
            'time_format' => 'H:i:s',
            'date_format' => 'Y-m-d',
        );
    }

    public function test_forum_number_format()
    {
        $result = forum_number_format(1234567);

        $this->assertIsString($result);
        $this->assertStringContainsString('1', $result);
    }

    public function test_forum_number_format_zero()
    {
        $result = forum_number_format(0);

        $this->assertEquals('0', $result);
    }

    public function test_forum_number_format_negative()
    {
        $result = forum_number_format(-123);

        $this->assertStringContainsString('-', $result);
        $this->assertStringContainsString('123', $result);
    }

    public function test_format_time()
    {
        $timestamp = 1609459200; // 2021-01-01 00:00:00 UTC

        $result = format_time($timestamp);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_format_time_custom_format()
    {
        $timestamp = 1609459200;

        $result = format_time($timestamp, false, null, 'Y-m-d');

        $this->assertStringContainsString('2021', $result);
    }

    public function test_get_base_url()
    {
        $result = get_base_url();

        $this->assertEquals('http://example.com/forum', $result);
    }

    public function test_get_base_url_protocol()
    {
        $result = get_base_url();

        $this->assertStringStartsWith('http://', $result, 'Base URL should start with http://');
    }

    public function test_validate_redirect_valid()
    {
        $result = validate_redirect('http://example.com/forum/viewtopic.php?id=123', 'index.php');

        $this->assertEquals('http://example.com/forum/viewtopic.php?id=123', $result);
    }

    public function test_validate_redirect_invalid()
    {
        $result = validate_redirect('http://evil.com', 'index.php');

        $this->assertEquals('index.php', $result, 'Should return default for external URLs');
    }

    public function test_validate_redirect_relative_fails()
    {
        // Relative URLs without host should fail validation
        $result = validate_redirect('viewtopic.php?id=123', 'index.php');

        $this->assertEquals('index.php', $result, 'Relative URLs should return fallback');
    }

    public function test_validate_redirect_empty()
    {
        $result = validate_redirect('', 'index.php');

        $this->assertEquals('index.php', $result, 'Should return default for empty redirect');
    }

    public function test_pun_htmlspecialchars()
    {
        $text = '<script>alert("XSS")</script>';
        $result = pun_htmlspecialchars($text);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function test_pun_htmlspecialchars_quotes()
    {
        $text = 'He said "Hello"';
        $result = pun_htmlspecialchars($text);

        $this->assertStringContainsString('&quot;', $result);
    }

    public function test_pun_htmlspecialchars_ampersand()
    {
        $text = 'Tom & Jerry';
        $result = pun_htmlspecialchars($text);

        $this->assertStringContainsString('&amp;', $result);
    }

    public function test_pun_strlen()
    {
        $text = 'Hello World';
        $result = pun_strlen($text);

        $this->assertEquals(11, $result);
    }

    public function test_pun_strlen_utf8()
    {
        $text = 'こんにちは'; // 5 Japanese characters
        $result = pun_strlen($text);

        $this->assertEquals(5, $result, 'Should count UTF-8 characters correctly');
    }

    public function test_pun_trim()
    {
        $text = '  Hello World  ';
        $result = pun_trim($text);

        $this->assertEquals('Hello World', $result);
    }

    public function test_pun_trim_empty()
    {
        $text = '   ';
        $result = pun_trim($text);

        $this->assertEquals('', $result);
    }

    public function test_forum_list_styles()
    {
        $styles = forum_list_styles();

        $this->assertIsArray($styles);
        $this->assertNotEmpty($styles, 'Should return available styles');
    }

    public function test_forum_list_langs()
    {
        $langs = forum_list_langs();

        $this->assertIsArray($langs);
        $this->assertNotEmpty($langs, 'Should return available languages');
    }

    public function test_paginate_basic()
    {
        $num_pages = 5;
        $cur_page = 1;
        $link = 'viewtopic.php?id=123';

        $result = paginate($num_pages, $cur_page, $link);

        $this->assertIsString($result);
    }

    public function test_paginate_single_page()
    {
        $num_pages = 1;
        $cur_page = 1;
        $link = 'viewtopic.php?id=123';

        $result = paginate($num_pages, $cur_page, $link);

        $this->assertStringNotContainsString('class="paging"', $result, 'Should not show pagination for single page');
    }

    public function test_paginate_multiple_pages()
    {
        $num_pages = 10;
        $cur_page = 5;
        $link = 'viewtopic.php?id=123';

        $result = paginate($num_pages, $cur_page, $link);

        // Check for pagination links
        $this->assertStringContainsString('<a', $result, 'Should contain anchor tags');
        $this->assertStringContainsString('<strong>5</strong>', $result, 'Current page should be in strong tags');
    }

    public function test_get_title_admin()
    {
        $GLOBALS['pun_bans'] = array();
        $GLOBALS['lang_common'] = array(
            'Member' => 'Member',
            'Guest' => 'Guest',
            'Banned' => 'Banned',
        );

        $user = array(
            'g_id' => PUN_ADMIN,
            'username' => 'admin',
            'title' => '',
            'g_user_title' => 'Administrator',
        );
        $result = get_title($user);

        $this->assertIsString($result);
        $this->assertEquals('Administrator', $result);
    }

    public function test_get_title_member()
    {
        $GLOBALS['pun_bans'] = array();
        $GLOBALS['lang_common'] = array(
            'Member' => 'Member',
            'Guest' => 'Guest',
            'Banned' => 'Banned',
        );

        $user = array(
            'g_id' => PUN_MEMBER,
            'username' => 'testuser',
            'title' => '',
            'g_user_title' => '',
        );
        $result = get_title($user);

        $this->assertIsString($result);
        $this->assertEquals('Member', $result);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['pun_config']['o_base_url']);
        unset($GLOBALS['pun_user']);
    }
}

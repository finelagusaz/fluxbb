<?php

// Bootstrap file for PHPUnit tests
// This file is loaded before any tests run

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define MB_OVERLOAD_STRING for PHP 8+ compatibility
if (!defined('MB_OVERLOAD_STRING')) {
    define('MB_OVERLOAD_STRING', 2);
}

// Define base paths
define('PUN_ROOT', dirname(__DIR__) . '/');
define('PUN', '');

// Load required files
require_once PUN_ROOT . 'include/functions.php';
require_once PUN_ROOT . 'include/utf8/utf8.php';

// Set up minimal globals for testing
$GLOBALS['pun_config'] = array(
    'o_base_url' => 'http://example.com/forum',
    'o_server_timezone' => 0,
    'o_timeout_visit' => 1800,
    'o_timeout_online' => 300,
    'o_show_dot' => '0',
    'o_topic_views' => '1',
    'o_smilies' => '1',
    'o_smilies_sig' => '1',
    'o_make_links' => '1',
    'o_censoring' => '0',
    'o_show_user_info' => '1',
    'o_show_post_count' => '1',
);

$GLOBALS['lang_common'] = array(
    'Bad request' => 'Bad request',
);

$GLOBALS['pun_user'] = array(
    'is_guest' => 1,
    'timezone' => 0,
    'dst' => 0,
    'time_format' => 'H:i:s',
    'date_format' => 'Y-m-d',
);

// Password hash cost for testing
$GLOBALS['password_hash_cost'] = 10;

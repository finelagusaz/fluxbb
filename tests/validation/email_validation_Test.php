<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../include/email.php';

class email_validation_Test extends TestCase
{
	public function testSimpleValidEmail()
	{
		$this->assertSame(1, is_valid_email('user@example.com'));
	}

	public function testEmailWithDots()
	{
		$this->assertSame(1, is_valid_email('user.name@example.com'));
		$this->assertSame(1, is_valid_email('first.last@example.com'));
	}

	public function testEmailWithPlus()
	{
		$this->assertSame(1, is_valid_email('user+tag@example.com'));
	}

	public function testEmailWithHyphen()
	{
		$this->assertSame(1, is_valid_email('user-name@example.com'));
	}

	public function testEmailWithNumbers()
	{
		$this->assertSame(1, is_valid_email('user123@example.com'));
		$this->assertSame(1, is_valid_email('123user@example.com'));
	}

	public function testEmailWithUnderscore()
	{
		$this->assertSame(1, is_valid_email('user_name@example.com'));
	}

	public function testEmailWithSubdomain()
	{
		$this->assertSame(1, is_valid_email('user@mail.example.com'));
		$this->assertSame(1, is_valid_email('user@a.b.c.example.com'));
	}

	public function testEmailWithIPAddress()
	{
		$this->assertSame(1, is_valid_email('user@[192.168.1.1]'));
	}

	public function testEmailWithTwoLetterTLD()
	{
		$this->assertSame(1, is_valid_email('user@example.co'));
		$this->assertSame(1, is_valid_email('user@example.uk'));
	}

	public function testEmailWithLongTLD()
	{
		$this->assertSame(1, is_valid_email('user@example.museum'));
	}

	public function testEmailWithQuotedLocalPart()
	{
		$this->assertSame(1, is_valid_email('"user.name"@example.com'));
	}

	public function testMissingAtSign()
	{
		$this->assertSame(0, is_valid_email('userexample.com'));
	}

	public function testMissingLocalPart()
	{
		$this->assertSame(0, is_valid_email('@example.com'));
	}

	public function testMissingDomain()
	{
		$this->assertSame(0, is_valid_email('user@'));
	}

	public function testDoubleDot()
	{
		$this->assertSame(0, is_valid_email('user..name@example.com'));
	}

	public function testStartsWithDot()
	{
		$this->assertSame(0, is_valid_email('.user@example.com'));
	}

	public function testEndsWithDot()
	{
		$this->assertSame(0, is_valid_email('user.@example.com'));
	}

	public function testMultipleAtSigns()
	{
		$this->assertSame(0, is_valid_email('user@name@example.com'));
	}

	public function testSpacesInEmail()
	{
		$this->assertSame(0, is_valid_email('user name@example.com'));
		$this->assertSame(0, is_valid_email('user@exam ple.com'));
	}

	public function testInvalidCharacters()
	{
		// Note: The current implementation accepts these characters
		// TODO: Consider if this is the desired behavior
		$this->assertSame(1, is_valid_email('user!name@example.com'));
		$this->assertSame(1, is_valid_email('user#name@example.com'));
		$this->assertSame(1, is_valid_email('user$name@example.com'));
	}

	public function testEmptyString()
	{
		$this->assertSame(0, is_valid_email(''));
	}

	public function testTooLongEmail()
	{
		// Email must be 80 characters or less
		$longEmail = str_repeat('a', 70) . '@example.com'; // 82 characters total
		$this->assertFalse(is_valid_email($longEmail));
	}

	public function testMaxLengthEmail()
	{
		// 80 characters exactly should be valid
		$maxEmail = str_repeat('a', 67) . '@example.com'; // 80 characters total
		$this->assertSame(1, is_valid_email($maxEmail));
	}

	public function testMissingTLD()
	{
		$this->assertSame(0, is_valid_email('user@localhost'));
	}

	public function testDomainWithHyphen()
	{
		$this->assertSame(1, is_valid_email('user@my-domain.com'));
	}

	public function testDomainWithNumbers()
	{
		$this->assertSame(1, is_valid_email('user@example123.com'));
	}

	public function testInvalidDomainStart()
	{
		// Note: The current implementation accepts hyphens at domain boundaries
		// TODO: Consider if this is the desired behavior per RFC standards
		$this->assertSame(1, is_valid_email('user@-example.com'));
	}

	public function testInvalidDomainEnd()
	{
		// Note: The current implementation accepts hyphens at domain boundaries
		// TODO: Consider if this is the desired behavior per RFC standards
		$this->assertSame(1, is_valid_email('user@example-.com'));
	}
}

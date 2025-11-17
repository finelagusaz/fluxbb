<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../include/functions.php';

class strip_bad_multibyte_chars_Test extends TestCase
{
	public function testPreservesNormalText()
	{
		$input = "Hello World";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesOneByteCharacters()
	{
		$input = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesTwoByteUTF8()
	{
		// Latin Extended characters (2-byte UTF-8)
		$input = "Café résumé";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesThreeByteUTF8()
	{
		// CJK characters (3-byte UTF-8)
		$input = "こんにちは世界";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);

		$input = "你好世界";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testReplacesFourByteEmoji()
	{
		// 4-byte UTF-8 emoji
		$input = "Hello 😀 World";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('Hello ? World', $result);
		$this->assertStringNotContainsString('😀', $result);
	}

	public function testReplacesMultipleFourByteCharacters()
	{
		$input = "Test 😀 😁 😂 emoji";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('Test ? ? ? emoji', $result);
	}

	public function testReplacesFourByteCJKExtensions()
	{
		// Some rare CJK characters use 4-byte UTF-8
		// Character in range 0xF0 (11110000) to 0xF4 (11110100)
		$input = "Test\xf0\x9f\x98\x80End";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('Test?End', $result);
	}

	public function testHandlesEmptyString()
	{
		$result = strip_bad_multibyte_chars('');
		$this->assertEquals('', $result);
	}

	public function testHandlesMixedContent()
	{
		$input = "ASCII こんにちは 😀 World";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('ASCII こんにちは ? World', $result);
	}

	public function testPreservesGreekCharacters()
	{
		$input = "Ελληνικά";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesCyrillicCharacters()
	{
		$input = "Привет мир";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesArabicCharacters()
	{
		$input = "مرحبا بالعالم";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testHandlesConsecutiveFourByteChars()
	{
		$input = "😀😁😂";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('???', $result);
	}

	public function testHandlesStringStartingWithFourByte()
	{
		$input = "😀Hello";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('?Hello', $result);
	}

	public function testHandlesStringEndingWithFourByte()
	{
		$input = "Hello😀";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('Hello?', $result);
	}

	public function testHandlesOnlyFourByteCharacter()
	{
		$input = "😀";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals('?', $result);
	}

	public function testPreservesSpecialSymbols()
	{
		// These are 3-byte UTF-8, not 4-byte
		$input = "© ® ™ € £ ¥";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testHandlesNewlinesAndTabs()
	{
		$input = "Line1\nLine2\tTabbed";
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals($input, $result);
	}

	public function testSecurityFourByteBufferOverflow()
	{
		// Test that 4-byte character doesn't cause buffer issues
		$input = str_repeat('😀', 100);
		$result = strip_bad_multibyte_chars($input);
		$this->assertEquals(str_repeat('?', 100), $result);
		$this->assertEquals(100, strlen($result));
	}
}

<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../include/functions.php';

class remove_bad_characters_Test extends TestCase
{
	public function testRemovesNullByte()
	{
		$input = "Hello\x00World";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
		$this->assertStringNotContainsString("\x00", $result);
	}

	public function testRemovesControlCharacters()
	{
		// Test various control characters (0x00-0x08, 0x0b-0x0c, 0x0e-0x1f)
		$input = "Hello\x01\x02\x03World";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testPreservesNewlines()
	{
		// \n (0x0a) should be preserved as it's not in the control character range
		$input = "Hello\nWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals("Hello\nWorld", $result);
	}

	public function testPreservesCarriageReturn()
	{
		// \r (0x0d) should be preserved as it's not in the control character range
		$input = "Hello\rWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals("Hello\rWorld", $result);
	}

	public function testRemovesZeroWidthSpace()
	{
		// ZERO WIDTH SPACE (U+200B)
		$input = "Hello\xe2\x80\x8bWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesZeroWidthNonJoiner()
	{
		// ZERO WIDTH NON-JOINER (U+200C)
		$input = "Hello\xe2\x80\x8cWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesZeroWidthJoiner()
	{
		// ZERO WIDTH JOINER (U+200D)
		$input = "Hello\xe2\x80\x8dWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesDirectionalMarks()
	{
		// LEFT-TO-RIGHT MARK (U+200E)
		$input = "Hello\xe2\x80\x8eWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);

		// RIGHT-TO-LEFT MARK (U+200F)
		$input = "Hello\xe2\x80\x8fWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesDirectionalEmbedding()
	{
		// LEFT-TO-RIGHT EMBEDDING (U+202A)
		$input = "Hello\xe2\x80\xaaWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);

		// RIGHT-TO-LEFT EMBEDDING (U+202B)
		$input = "Hello\xe2\x80\xabWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesDirectionalOverride()
	{
		// LEFT-TO-RIGHT OVERRIDE (U+202D)
		$input = "Hello\xe2\x80\xadWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);

		// RIGHT-TO-LEFT OVERRIDE (U+202E)
		$input = "Hello\xe2\x80\xaeWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesByteOrderMark()
	{
		// ZERO WIDTH NO-BREAK SPACE / BOM (U+FEFF)
		$input = "\xef\xbb\xbfHello World";
		$result = remove_bad_characters($input);
		$this->assertEquals('Hello World', $result);
	}

	public function testReplacesSpecialSpacesWithNormalSpace()
	{
		// EN QUAD (U+2000)
		$input = "Hello\xe2\x80\x80World";
		$result = remove_bad_characters($input);
		$this->assertEquals('Hello World', $result);

		// EM QUAD (U+2001)
		$input = "Hello\xe2\x80\x81World";
		$result = remove_bad_characters($input);
		$this->assertEquals('Hello World', $result);

		// IDEOGRAPHIC SPACE (U+3000)
		$input = "Hello\xE3\x80\x80World";
		$result = remove_bad_characters($input);
		$this->assertEquals('Hello World', $result);
	}

	public function testHandlesArrayInput()
	{
		$input = array(
			'field1' => "Hello\x00World",
			'field2' => "Test\xe2\x80\x8bValue"
		);
		$result = remove_bad_characters($input);

		$this->assertIsArray($result);
		$this->assertEquals('HelloWorld', $result['field1']);
		$this->assertEquals('TestValue', $result['field2']);
	}

	public function testHandlesNestedArrays()
	{
		$input = array(
			'level1' => array(
				'level2' => "Hello\x00World"
			)
		);
		$result = remove_bad_characters($input);

		$this->assertIsArray($result);
		$this->assertEquals('HelloWorld', $result['level1']['level2']);
	}

	public function testPreservesNormalText()
	{
		$input = "Hello World! This is a normal string.";
		$result = remove_bad_characters($input);
		$this->assertEquals($input, $result);
	}

	public function testPreservesUTF8Characters()
	{
		$input = "こんにちは世界";
		$result = remove_bad_characters($input);
		$this->assertEquals($input, $result);
	}

	public function testHandlesEmptyString()
	{
		$result = remove_bad_characters('');
		$this->assertEquals('', $result);
	}

	public function testHandlesEmptyArray()
	{
		$result = remove_bad_characters(array());
		$this->assertEquals(array(), $result);
	}

	public function testRemovesMultipleBadCharacters()
	{
		$input = "Hello\x00\xe2\x80\x8b\x01World\xef\xbb\xbf";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesHangulFillers()
	{
		// HANGUL CHOSEONG FILLER (U+115F)
		$input = "Hello\xe1\x85\x9fWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);

		// HANGUL JUNGSEONG FILLER (U+1160)
		$input = "Hello\xe1\x85\xA0World";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);

		// HANGUL FILLER (U+3164)
		$input = "Hello\xe3\x85\xa4World";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testRemovesReplacementCharacter()
	{
		// REPLACEMENT CHARACTER (U+FFFD)
		$input = "Hello\xef\xbf\xbdWorld";
		$result = remove_bad_characters($input);
		$this->assertEquals('HelloWorld', $result);
	}

	public function testSecurityControlCharacterInjection()
	{
		// Test potential SQL injection with null bytes
		$input = "admin\x00-- ";
		$result = remove_bad_characters($input);
		$this->assertEquals('admin-- ', $result);
		$this->assertStringNotContainsString("\x00", $result);
	}

	public function testSecurityDirectionalOverrideAttack()
	{
		// Test potential homograph attack using directional override
		$input = "moc.elpmaxe\xe2\x80\xae@resu";
		$result = remove_bad_characters($input);
		$this->assertStringNotContainsString("\xe2\x80\xae", $result);
	}
}

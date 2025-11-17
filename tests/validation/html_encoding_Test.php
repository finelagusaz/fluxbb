<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../include/functions.php';

class html_encoding_Test extends TestCase
{
	public function testEncodesAmpersand()
	{
		$this->assertEquals('&amp;', pun_htmlspecialchars('&'));
	}

	public function testEncodesLessThan()
	{
		$this->assertEquals('&lt;', pun_htmlspecialchars('<'));
	}

	public function testEncodesGreaterThan()
	{
		$this->assertEquals('&gt;', pun_htmlspecialchars('>'));
	}

	public function testEncodesDoubleQuote()
	{
		$this->assertEquals('&quot;', pun_htmlspecialchars('"'));
	}

	public function testEncodesSingleQuote()
	{
		$this->assertEquals('&#039;', pun_htmlspecialchars("'"));
	}

	public function testEncodesScriptTag()
	{
		$result = pun_htmlspecialchars('<script>alert("XSS")</script>');
		$this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $result);
	}

	public function testEncodesHtmlTag()
	{
		$result = pun_htmlspecialchars('<div class="test">Content</div>');
		$this->assertEquals('&lt;div class=&quot;test&quot;&gt;Content&lt;/div&gt;', $result);
	}

	public function testEncodesEventHandler()
	{
		$result = pun_htmlspecialchars('<img src="x" onerror="alert(1)">');
		$this->assertEquals('&lt;img src=&quot;x&quot; onerror=&quot;alert(1)&quot;&gt;', $result);
	}

	public function testPreservesNormalText()
	{
		$this->assertEquals('Hello World', pun_htmlspecialchars('Hello World'));
	}

	public function testPreservesNumbers()
	{
		$this->assertEquals('12345', pun_htmlspecialchars('12345'));
	}

	public function testHandlesEmptyString()
	{
		$this->assertEquals('', pun_htmlspecialchars(''));
	}

	public function testHandlesUtf8Characters()
	{
		$this->assertEquals('こんにちは', pun_htmlspecialchars('こんにちは'));
		$this->assertEquals('Ñoño', pun_htmlspecialchars('Ñoño'));
		$this->assertEquals('Ελληνικά', pun_htmlspecialchars('Ελληνικά'));
	}

	public function testHandlesUtf8WithSpecialChars()
	{
		$result = pun_htmlspecialchars('こんにちは<script>alert("XSS")</script>');
		$this->assertEquals('こんにちは&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $result);
	}

	public function testEncodesMultipleSpecialChars()
	{
		$result = pun_htmlspecialchars('&lt;&gt;&amp;&quot;&apos;');
		$this->assertEquals('&amp;lt;&amp;gt;&amp;amp;&amp;quot;&amp;apos;', $result);
	}

	public function testHandlesComplexXSSAttempt()
	{
		$xss = '<IMG SRC=j&#X41vascript:alert(\'test2\')>';
		$result = pun_htmlspecialchars($xss);
		$this->assertStringNotContainsString('<IMG', $result);
		$this->assertStringContainsString('&lt;IMG', $result);
	}

	public function testHandlesUrlWithSpecialChars()
	{
		$result = pun_htmlspecialchars('http://example.com?foo=bar&baz=qux');
		$this->assertEquals('http://example.com?foo=bar&amp;baz=qux', $result);
	}

	public function testHandlesDataUri()
	{
		$result = pun_htmlspecialchars('data:text/html,<script>alert("XSS")</script>');
		$this->assertStringContainsString('&lt;script&gt;', $result);
	}
}

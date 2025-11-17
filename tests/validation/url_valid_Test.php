<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../include/functions.php';

class url_valid_Test extends TestCase
{
	public function testValidHttpUrl()
	{
		$result = url_valid('http://example.com');
		$this->assertIsArray($result);
		$this->assertEquals('http', $result['scheme']);
		$this->assertEquals('example.com', $result['host']);
	}

	public function testValidHttpsUrl()
	{
		$result = url_valid('https://example.com');
		$this->assertIsArray($result);
		$this->assertEquals('https', $result['scheme']);
	}

	public function testValidFtpUrl()
	{
		$result = url_valid('ftp://ftp.example.com');
		$this->assertIsArray($result);
		$this->assertEquals('ftp', $result['scheme']);
	}

	public function testValidFtpsUrl()
	{
		$result = url_valid('ftps://ftp.example.com');
		$this->assertIsArray($result);
		$this->assertEquals('ftps', $result['scheme']);
	}

	public function testWwwPrefixAutoConvertsToHttp()
	{
		$result = url_valid('www.example.com');
		$this->assertIsArray($result);
		$this->assertEquals('http', $result['scheme']);
		$this->assertEquals('http://www.example.com', $result['url']);
	}

	public function testFtpPrefixAutoConvertsToFtp()
	{
		$result = url_valid('ftp.example.com');
		$this->assertIsArray($result);
		$this->assertEquals('ftp', $result['scheme']);
		$this->assertEquals('ftp://ftp.example.com', $result['url']);
	}

	public function testUrlWithPath()
	{
		$result = url_valid('http://example.com/path/to/page');
		$this->assertIsArray($result);
		$this->assertEquals('/path/to/page', $result['path_abempty']);
	}

	public function testUrlWithQuery()
	{
		$result = url_valid('http://example.com/page?foo=bar&baz=qux');
		$this->assertIsArray($result);
		$this->assertStringContainsString('foo=bar', $result['query']);
	}

	public function testUrlWithFragment()
	{
		$result = url_valid('http://example.com/page#section');
		$this->assertIsArray($result);
		$this->assertEquals('section', $result['fragment']);
	}

	public function testUrlWithPort()
	{
		$result = url_valid('http://example.com:8080/');
		$this->assertIsArray($result);
		$this->assertEquals('8080', $result['port']);
	}

	public function testUrlWithIPv4()
	{
		$result = url_valid('http://192.168.1.1/');
		$this->assertIsArray($result);
		$this->assertEquals('192.168.1.1', $result['host']);
	}

	public function testUrlWithIPv6()
	{
		$result = url_valid('http://[2001:db8::1]/');
		$this->assertIsArray($result);
		$this->assertStringContainsString('2001:db8::1', $result['host']);
	}

	public function testUrlWithSubdomain()
	{
		$result = url_valid('http://sub.example.com/');
		$this->assertIsArray($result);
		$this->assertEquals('sub.example.com', $result['host']);
	}

	public function testUrlWithInternationalDomainName()
	{
		$result = url_valid('http://xn--example.com/');
		$this->assertIsArray($result);
		$this->assertIsArray($result);
	}

	public function testHttpWithUserinfoIsInvalid()
	{
		$result = url_valid('http://user:pass@example.com/');
		$this->assertFalse($result);
	}

	public function testHttpsWithUserinfoIsInvalid()
	{
		$result = url_valid('https://user:pass@example.com/');
		$this->assertFalse($result);
	}

	public function testFtpWithUserinfoIsValid()
	{
		$result = url_valid('ftp://user:pass@ftp.example.com/');
		$this->assertIsArray($result);
		$this->assertEquals('user:pass', $result['userinfo']);
	}

	public function testInvalidScheme()
	{
		$result = url_valid('javascript:alert(1)');
		$this->assertFalse($result);
	}

	public function testMissingScheme()
	{
		$result = url_valid('example.com');
		$this->assertFalse($result);
	}

	public function testRelativeUrl()
	{
		$result = url_valid('/path/to/page');
		$this->assertFalse($result);
	}

	public function testEmptyString()
	{
		$result = url_valid('');
		$this->assertFalse($result);
	}

	public function testInvalidCharacters()
	{
		$result = url_valid('http://exam ple.com');
		$this->assertFalse($result);
	}

	public function testUrlWithPercentEncoding()
	{
		$result = url_valid('http://example.com/path%20with%20spaces');
		$this->assertIsArray($result);
		$this->assertStringContainsString('%20', $result['path_abempty']);
	}

	public function testUrlWithMultipleSubdomains()
	{
		$result = url_valid('http://a.b.c.example.com/');
		$this->assertIsArray($result);
		$this->assertEquals('a.b.c.example.com', $result['host']);
	}

	public function testUrlWithDashInDomain()
	{
		$result = url_valid('http://my-site.example.com/');
		$this->assertIsArray($result);
		$this->assertEquals('my-site.example.com', $result['host']);
	}

	public function testComplexValidUrl()
	{
		$result = url_valid('https://sub.example.com:443/path/to/page?query=value&foo=bar#section');
		$this->assertIsArray($result);
		$this->assertEquals('https', $result['scheme']);
		$this->assertEquals('sub.example.com', $result['host']);
		$this->assertEquals('443', $result['port']);
	}
}

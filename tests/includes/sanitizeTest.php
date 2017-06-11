<?php

require(__DIR__ . '/../../includes/sanitize.php');

use PHPUnit\Framework\TestCase;

/**
 * @covers Sanitize
 */
final class SanitizeTest extends TestCase
{

	/*
	 * sanitizeFilenameAndCheckBlacklist
	 */

	public function testSanitizeFilenameAndCheckBlacklist()
	{
		$result = Sanitize::sanitizeFilenameAndCheckBlacklist('abe$er&%*#$');
		$this->assertEquals("abeer", $result);
	}

	/*
	 * isFilenameBlacklisted
	 */

	public function testIsFilenameBlacklisted()
	{
		//Will always fail because
		$result = Sanitize::isFilenameBlacklisted('bryan.exe');
		$this->assertTrue($result);
	}

	/*
	 * encodeStringForDisplay
	 */

	public function testEncodeStringForDisplayTagsOnly()
	{
		$result = Sanitize::encodeStringForDisplay("<h1>Hello, world!</h1>");
		$this->assertEquals("&lt;h1&gt;Hello, world!&lt;/h1&gt;", $result);
	}

	public function testEncodeStringForDisplayNoDoubleEncode()
	{
		$result = Sanitize::encodeStringForDisplay("Here &amp; there.");
		$this->assertEquals("Here &amp; there.", $result);
	}

	public function testEncodeStringForDisplayTagWithAttributeSingleQuotes()
	{
		$result = Sanitize::encodeStringForDisplay("<h1 attr='blue'>");
		$this->assertEquals("&lt;h1 attr=&#039;blue&#039;&gt;", $result);
	}

	public function testEncodeStringForDisplayTagWithAttributeDoubleQuotes()
	{
		$result = Sanitize::encodeStringForDisplay('<h1 attr="blue">');
		$this->assertEquals("&lt;h1 attr=&quot;blue&quot;&gt;", $result);
	}

	public function testEncodeStringForDisplayTagCheckEverything()
	{
		$result = Sanitize::encodeStringForDisplay(
			"<h1 color='blue'>Here &amp; there.</h1> <h2 color=\"red\">It works!</h2>");
		$this->assertEquals("&lt;h1 color=&#039;blue&#039;&gt;Here &amp; there.&lt;/h1&gt;"
			. " &lt;h2 color=&quot;red&quot;&gt;It works!&lt;/h2&gt;", $result);
	}

	/*
	 * encodeStringForJavascript
	 */

	public function testEncodeStringForJavascript()
	{
		$result = Sanitize::encodeStringForJavascript("<h1 style='color: red;'>Hello, world!</h1>");
		$this->assertEquals(
			'\x3ch1\x20style\x3d\x27color\x3a\x20red\x3b\x27\x3eHello\x2c\x20world\x21\x3c\x2fh1\x3e', $result);
	}

	/*
	 * encodeStringForCSS
	 */

	public function testEncodeStringForCSS()
	{
		$result = Sanitize::encodeStringForCSS("<h1 style='color: red;'>Hello, world!</h1>");
		$this->assertEquals('\3ch1\20style\3d\27color\3a\20red\3b\27\3eHello\2c\20world\21\3c\2fh1\3e', $result);
	}

	/*
	 * encodeUrlParam
	 */

	public function testEncodeUrlParam()
	{
		$result = Sanitize::encodeUrlParam("<h1>Hello, world!</h1>");
		$this->assertEquals("%3Ch1%3EHello%2C+world%21%3C%2Fh1%3E", $result);
	}

	/*
	 * fullUrl
	 */

	public function testFullUrlValid()
	{
		$testUrl = 'https://www.test.example.com/index.html?page-id=123&validchars=a-_~:;/?#[321]@!$(\'z\')*+,%b';
		$result = Sanitize::fullUrl($testUrl);
		$this->assertEquals($testUrl, $result);
	}

	public function testFullUrlWithInvalid()
	{
		$testUrl = "https://www.test.example.com/index.html?page-id=123&invalid=<h1>\"^Hello, world!\"</h1>";
		$expectedUrl = "https://www.test.example.com/index.html?page-id=123&invalid=h1Hello,world!/h1";
		$result = Sanitize::fullUrl($testUrl);
		$this->assertEquals($expectedUrl, $result);
	}

	/*
	 * stripHtmlTags
	 */

	public function testStripHtmlTags()
	{
		$result = Sanitize::stripHtmlTags("<h1>Yolo World!</h1>");
		$this->assertEquals("Yolo World!", $result);
	}

	/*
	 * onlyInt
	 */

	public function testOnlyInt()
	{
		$result = Sanitize::onlyInt("123");
		$this->assertEquals(123, $result);
	}

	public function testOnlyIntWithLettersAfter()
	{
		$result = Sanitize::onlyInt("123asdf");
		$this->assertEquals(123, $result);
	}

	public function testOnlyIntWithLettersBefore()
	{
		$result = Sanitize::onlyInt("asdf123");
		$this->assertEquals(123, $result);
	}

	public function testOnlyIntWithLettersBeforeAndAfter()
	{
		$result = Sanitize::onlyInt("asdf123asdf");
		$this->assertEquals(123, $result);
	}

	public function testOnlyIntLargerThanPhpMax()
	{
		$result = Sanitize::onlyInt(PHP_INT_MAX . "1234567890");
		$this->assertEquals(PHP_INT_MAX . "1234567890", $result);
	}

	/*
	 * onlyFloat
	 */

	public function testOnlyFloat()
	{
		$result = Sanitize::onlyFloat("123.001");
		$this->assertEquals(123.001, $result);
	}

	public function testOnlyFloatWithLettersAfter()
	{
		$result = Sanitize::onlyFloat("123.001asdf");
		$this->assertEquals(123.001, $result);
	}

	public function testOnlyFloatWithLettersBefore()
	{
		$result = Sanitize::onlyFloat("asdf123.001");
		$this->assertEquals(123.001, $result);
	}

	public function testOnlyFloatWithLettersBeforeAndAfter()
	{
		$result = Sanitize::onlyFloat("asdf123.001asdf");
		$this->assertEquals(123.001, $result);
	}

	/*
	 * domainNameWithoutPort
	 */

	public function testDomainNameWithoutPort()
	{
		$result = Sanitize::domainNameWithoutPort("www.example.com");
		$this->assertEquals("www.example.com", $result);
	}

	public function testDomainNameWithoutPortWithPath()
	{
		$result = Sanitize::domainNameWithoutPort("www.example.com/asdf");
		$this->assertEquals("www.example.comasdf", $result);
	}

	public function testDomainNameWithoutPortWithPathAndPort()
	{
		$result = Sanitize::domainNameWithoutPort("www.example.com:8080/asdf");
		$this->assertEquals("www.example.com8080asdf", $result);
	}

	public function testDomainNameWithoutPortWithFullUrl()
	{
		$result = Sanitize::domainNameWithoutPort("http://www.example.com:8080/asdf");
		$this->assertEquals("httpwww.example.com8080asdf", $result);
	}

	/*
	 * domainNameWithPort
	 */

	public function testDomainNameWithPort()
	{
		$result = Sanitize::domainNameWithPort("www.example.com");
		$this->assertEquals("www.example.com", $result);
	}

	public function testDomainNameWithPortWithPath()
	{
		$result = Sanitize::domainNameWithPort("www.example.com/asdf");
		$this->assertEquals("www.example.comasdf", $result);
	}

	public function testDomainNameWithPortWithPathAndPort()
	{
		$result = Sanitize::domainNameWithPort("www.example.com:8080/asdf");
		$this->assertEquals("www.example.com:8080", $result);
	}

	public function testDomainNameWithPortWithMultiplePorts()
	{
		$result = Sanitize::domainNameWithPort("www.example.com:8080:8080");
		$this->assertEquals("www.example.com:8080", $result);
	}

	/*
	 * emailAddress
	 */

	public function testEmailAddress()
	{
		$result = Sanitize::emailAddress("user@example.com");
		$this->assertEquals("user@example.com", $result);
	}

	public function testEmailAddressWithSubdomain()
	{
		$result = Sanitize::emailAddress("user@sub.example.com");
		$this->assertEquals("user@sub.example.com", $result);
	}

	public function testEmailAddressWithInvalidCharactersBefore()
	{
		$result = Sanitize::emailAddress("(\")user@sub.example.com");
		$this->assertEquals("user@sub.example.com", $result);
	}

	public function testEmailAddressWithInvalidCharactersAfter()
	{
		$result = Sanitize::emailAddress("user@sub.example.com(\")");
		$this->assertEquals("user@sub.example.com", $result);
	}

	public function testEmailAddressWithInvalidCharactersBeforeAndAfter()
	{
		$result = Sanitize::emailAddress("(\")user@sub.example.com(\")");
		$this->assertEquals("user@sub.example.com", $result);
	}

	/*
	 * courseId
	 */

	public function testSanitizeCourseIdAsInteger()
	{
		$result = Sanitize::courseId(123);
		$this->assertEquals(123, $result);
	}

	public function testSanitizeCourseIdAsString()
	{
		$result = Sanitize::courseId("123");
		$this->assertEquals(123, $result);
	}

	public function testSanitizeCourseIdWithAdminString()
	{
		$result = Sanitize::courseId("admin");
		$this->assertEquals("admin", $result);
	}

	public function testSanitizeCourseIdWithAdminStringWithSpaces()
	{
		$result = Sanitize::courseId("  admin  ");
		$this->assertEquals("admin", $result);
	}

	public function testSanitizeCourseIdEmpty()
	{
		$result = Sanitize::courseId("adsf");
		$this->assertEmpty($result);
	}

	/*
	 * generateQueryPlaceholders
	 */

	public function testGenerateQueryPlaceholders()
	{
		$result = Sanitize::generateQueryPlaceholders(array(1, 2, 3, 4));
		$this->assertEquals("?, ?, ?, ?", $result);
	}

	/*
	 * generateQueryPlaceholdersGrouped
	 */

	public function testGenerateQueryPlaceholdersGrouped()
	{
		$array = array(1, 2, 3, 4, 5, 6, 7, 8, 9);

		$result = Sanitize::generateQueryPlaceholdersGrouped($array, 3);
		$this->assertEquals("(?, ?, ?), (?, ?, ?), (?, ?, ?)", $result);
	}

	public function testGenerateQueryPlaceholdersGroupedIncorrectGroupSize()
	{
		$array = array(1, 2, 3, 4, 5, 6, 7, 8, 9);

		$result = Sanitize::generateQueryPlaceholdersGrouped($array, 4);
		$this->assertNull($result);
	}


}
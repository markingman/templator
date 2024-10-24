<?php

namespace Templator;

use Exception;
use PHPUnit\Framework\TestCase;

class ViewHTMLTest extends TestCase
{
	protected ViewHTML $ViewHTML;

	public function setUp(): void
	{
		$this->ViewHTML = new ViewHTML('');
	}

	public function testCreate(): void
	{
		$this->assertInstanceOf(ViewHTMLInterface::class, $this->ViewHTML);
	}

	public function testHtmlEntities(): void
	{
		$res = $this->ViewHTML->htmlentities('"&foo');
		$this->assertEquals('&quot;&amp;foo', $res);
	}

	public function testHtmlSpecialChars(): void
	{
		$res = $this->ViewHTML->htmlspecialchars('"&foo');
		$this->assertEquals('"&amp;foo', $res, 'Should encode special chars');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz">bar</i></p>');
		$this->assertEquals('foobar', $res, 'Should strip tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz">bar</i></p>', ['i']);
		$this->assertEquals('foo<i class="zzz">bar</i>', $res, 'Should persist specified tag');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz"><b>bar</b></i></p>', ['i', 'p']);
		$this->assertEquals('<p>foo<i class="zzz">bar</i></p>', $res, 'Should persist specified tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo</p>', null, false);
		$this->assertEquals('&lt;p&gt;foo&lt;/p&gt;', $res, 'Should encode tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<b>bar</b></p>', ['p'], false);
		$this->assertEquals('<p>foo&lt;b&gt;bar&lt;/b&gt;</p>', $res, 'Should encode tags and persist specified tag');

		$res = $this->ViewHTML->htmlspecialchars('<p>' . chr(2) . 'foo<b>bar</b></p>', ['p'], false);
		$this->assertEquals('<p>foo&lt;b&gt;bar&lt;/b&gt;</p>', $res, 'Should encode tags and persist specified tag');

		$res = $this->ViewHTML->htmlspecialchars('<p>' . chr(3) . 'foo<b>bar</b></p>', ['p'], false);
		$this->assertEquals('<p>foo&lt;b&gt;bar&lt;/b&gt;</p>', $res, 'Should encode tags and persist specified tag');

		$res = $this->ViewHTML->htmlspecialchars('<p>' . chr(3) . 'foo<b>bar</b>' . chr(2) . '</p>', ['p'], false);
		$this->assertEquals('<p>foo&lt;b&gt;bar&lt;/b&gt;</p>', $res, 'Should encode tags and persist specified tag');

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Could not replace tags due to preg_replace error');

		$res = $this->ViewHTML->htmlspecialchars('<b>bar</b>', ['~'], false);
		$this->assertEquals('&gt;bar&lt;', $res, '..');
	}

	public function testHtmlAtts(): void
	{
		$res = $this->ViewHTML->htmlatts(['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals('foo="bar" bool esc="&quot;"', $res, 'Should create attributes');

		$res = $this->ViewHTML->htmlatts(['foo' => 'bar', 'bool' => true, 'esc' => '"'], ['bool', 'esc']);
		$this->assertEquals('foo="bar"', $res, 'Should create filtered attributes');
	}

	public function testTag(): void
	{
		$res = $this->ViewHTML->tag('br/');
		$this->assertEquals('<br />', $res, 'Should make closed tag');

		$res = $this->ViewHTML->tag('hr');
		$this->assertEquals('<hr>', $res, 'Should make plain tag');

		$res = $this->ViewHTML->tag('/p');
		$this->assertEquals('</p>', $res, 'Should make close tag');

		$res = $this->ViewHTML->tag('tag/', ['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals('<tag foo="bar" bool esc="&quot;" />', $res, 'Should make closed tag with attributes');

		$res = $this->ViewHTML->tag('p/', null, 'Hello, <b>world</b>');
		$this->assertEquals('<p>Hello, <b>world</b></p>', $res, 'Should make closed tag with HTML contents');

		$res = $this->ViewHTML->tag('div/', ['data-foo' => 'bar', 'data-esc' => '"'], '<p><a>test</a></p>');
		$this->assertEquals('<div data-foo="bar" data-esc="&quot;"><p><a>test</a></p></div>', $res, 'Should make tag with attributes and HTML contents');

		$res = $this->ViewHTML->tag('input/', ['readonly' => true, 'value' => "VALUE"]);
		$this->assertEquals('<input readonly value="VALUE" />', $res, 'Should make tag with boolean attributes');

		$res = $this->ViewHTML->tag('tag/', ['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals('<tag foo="bar" bool esc="&quot;" />', $res, 'Should make closed tag with attributes');
	}
}

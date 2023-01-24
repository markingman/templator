<?php

use Templator\ViewHTML;
use Templator\ViewHTMLInterface;
use PHPUnit\Framework\TestCase;

class ViewHTMLTest extends TestCase
{
	protected $ViewHTML;

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
		$this->assertEquals($res, '&quot;&amp;foo');
	}

	public function testHtmlSpecialChars(): void
	{
		$res = $this->ViewHTML->htmlspecialchars('"&foo');
		$this->assertEquals($res, '"&amp;foo', 'Should encode special chars');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz">bar</i></p>');
		$this->assertEquals($res, 'foobar', 'Should strip tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz">bar</i></p>', ['i']);
		$this->assertEquals($res, 'foo<i class="zzz">bar</i>', 'Should persist specified tag');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<i class="zzz"><b>bar</b></i></p>', ['i', 'p']);
		$this->assertEquals($res, '<p>foo<i class="zzz">bar</i></p>', 'Should persist specified tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo</p>', null, false);
		$this->assertEquals($res, '&lt;p&gt;foo&lt;/p&gt;', 'Should encode tags');

		$res = $this->ViewHTML->htmlspecialchars('<p>foo<b>bar</b></p>', ['p'], false);
		$this->assertEquals($res, '<p>foo&lt;b&gt;bar&lt;/b&gt;</p>', 'Should encode tags and persist specified tag');
	}

	public function testHtmlAtts(): void
	{
		$res = $this->ViewHTML->htmlatts(['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals($res, 'foo="bar" bool esc="&quot;"', 'Should create attributes');

		$res = $this->ViewHTML->htmlatts(['foo' => 'bar', 'bool' => true, 'esc' => '"'], ['bool', 'esc']);
		$this->assertEquals($res, 'foo="bar"', 'Should create filtered attributes');
	}

	public function testTag(): void
	{
		$res = $this->ViewHTML->tag('br/');
		$this->assertEquals($res, '<br />', 'Should make closed tag');

		$res = $this->ViewHTML->tag('hr');
		$this->assertEquals($res, '<hr>', 'Should make plain tag');

		$res = $this->ViewHTML->tag('/p');
		$this->assertEquals($res, '</p>', 'Should make close tag');

		$res = $this->ViewHTML->tag('tag/', ['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals($res, '<tag foo="bar" bool esc="&quot;" />', 'Should make closed tag with attributes');

		$res = $this->ViewHTML->tag('p/', null, 'Hello, <b>world</b>');
		$this->assertEquals($res, '<p>Hello, <b>world</b></p>', 'Should make closed tag with HTML contents');

		$res = $this->ViewHTML->tag('div/', ['foo' => 'bar', 'bool' => true, 'esc' => '"'], '<p><a>test</a></p>');
		$this->assertEquals($res, '<div foo="bar" bool esc="&quot;"><p><a>test</a></p></div>', 'Should make tag with attributes and HTML contents');

		$res = $this->ViewHTML->tag('tag/', ['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertEquals($res, '<tag foo="bar" bool esc="&quot;" />', 'Should make closed tag with attributes');
	}
}

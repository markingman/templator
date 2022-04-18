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

	public function testCreate()
	{
		$this->assertInstanceOf(ViewHTMLInterface::class, $this->ViewHTML);
	}

	public function testHtmlEntities()
	{
		$res = $this->ViewHTML->htmlentities('"&foo');
		$this->assertTrue($res === '&quot;&amp;foo');
	}

	public function testHtmlSpecialChars()
	{
		$res = $this->ViewHTML->htmlspecialchars('"&foo');
		$this->assertTrue($res === '"&amp;foo');
	}

	public function testHtmlAtts()
	{
		$res = $this->ViewHTML->htmlatts(['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertTrue($res === 'foo="bar" bool esc="&quot;"');
	}

	public function testTag()
	{
		$res = $this->ViewHTML->tag('br/');
		$this->assertTrue($res === '<br />');

		$res = $this->ViewHTML->tag('hr');
		$this->assertTrue($res === '<hr>');

		$res = $this->ViewHTML->tag('/p');
		$this->assertTrue($res === '</p>');

		$res = $this->ViewHTML->tag('tag/', ['foo' => 'bar', 'bool' => true, 'esc' => '"']);
		$this->assertTrue($res === '<tag foo="bar" bool esc="&quot;" />');
	}
}

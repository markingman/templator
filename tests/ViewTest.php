<?php

use Templator\View;
use Templator\ViewInterface;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
	use TmpDirTestHelpersTrait;

	protected View $View;

	public static function setUpBeforeClass(): void
	{
		static::tmpdir_make(self::class);
	}

	public function setUp(): void
	{
		$this->View = new View(static::$tmpdir);
	}

	public function testCreate(): void
	{
		$this->assertInstanceOf(ViewInterface::class, $this->View);
	}

	public function testGetMissing(): void
	{
		$view = '--missing--';
		$res = $this->View->get($view);
		$this->assertTrue($res === '');
	}

	public function testGetTemplateString(): void
	{
		$view = 'string.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

echo 'Hello World';

__
		);
		$res = $this->View->get($view);
		$this->assertTrue($res === 'Hello World');
	}

	public function testGetTemplateStringPHPTags(): void
	{
		$view = 'stringphptags.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

?>AA<?php
echo 'Hello World';
?>BB<?php

__
		);
		$res = $this->View->get($view);
		$this->assertTrue($res === 'AAHello WorldBB');
	}

	public function testGetTemplateData(): void
	{
		$view = 'array.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

return ['a' => 1, 'b' => 2];

__
		);
		$res = $this->View->get($view);
		$this->assertEquals(['a' => 1, 'b' => 2], $res);
	}

	public function testTemplateVars(): void
	{
		$view = 'vars1.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

if (@\$var === true) {
	return 'TRUE';
} else {
	return 'FALSE';
}

__
		);
		$res = $this->View->get($view, ['var' => true]);
		$this->assertEquals('TRUE', $res);
	}

	public function testTemplateVarsOb(): void
	{
		$view = 'vars2.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

return 10;

__
		);
		$res = $this->View->get($view, null, false);
		$this->assertEquals(10, $res);

		$res = $this->View->get($view);
		$this->assertEquals(10, $res);

		$view = 'vars2.1.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

return 1;

__
		);
		$res = $this->View->get($view, null, false);
		$this->assertEquals(1, $res);

		// assume ob doesn't work if result might be === 1
		$res = $this->View->get($view);
		$this->assertEquals('', $res);
	}

	public function testGetTemplateNested(): void
	{
		$view = 'nested1.php';
		file_put_contents(static::$tmpdir . '/' . $view, <<<__
<?php

echo 'nested1';
echo \$this->get('nested2.php', ['var' => 'VAR']);
echo 'END';

__
		);

		file_put_contents(static::$tmpdir . '/nested2.php', <<<__
<?php

echo '2';
if (\$var === 'VAR') {echo 'OK';}

__
		);

		$res = $this->View->get($view);
		$this->assertEquals($res, 'nested1' . '2' . 'OK' . 'END');
	}

	public static function tearDownAfterClass(): void
	{
		static::tmpdir_remove();
	}
}

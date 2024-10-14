<?php

namespace Templator;

use Exception;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
	use TmpDirTestHelpersTrait;

	protected View $View;

	public static function setUpBeforeClass(): void
	{
		static::tmpdir_make();
	}

	public static function tearDownAfterClass(): void
	{
		static::tmpdir_remove();
	}

	public function setUp(): void
	{
		$this->View = new View(static::$tmpdir);
	}

	public function testCreate(): void
	{
		$this->assertInstanceOf(ViewInterface::class, $this->View);
	}

	public function testCreateFailure(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Could not set path');
   		new View('./--missing--');
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

	public function testFetch(): void
	{
		$tmpl = 'tmpl.php';
		file_put_contents(static::$tmpdir . '/' . $tmpl, <<<__
<?php

return function() {};

__
		);

		$res = $this->View->fetch($tmpl);
		$this->assertIsCallable($res);
	}

	public function testFetchAndCall(): void
	{
		$tmpl = 'tmpl.php';
		file_put_contents(static::$tmpdir . '/' . $tmpl, <<<__
<?php

return function(): string {
	return 'abc';
};

__
		);
		$res = $this->View->fetch($tmpl)();
		$this->assertEquals('abc', $res);
	}

	public function testFetchAndCallWithArgs(): void
	{
		$tmpl = 'tmpl.php';
		file_put_contents(static::$tmpdir . '/' . $tmpl, <<<__
<?php

return function(bool \$a, string \$b, array \$c): ?array {
	return (\$a and \$b === 'B' and \$c === [1]) ? [1, 2, 3] : null;
};

__
		);
		$res = $this->View->fetch($tmpl)(true, 'B', [1]);
		$this->assertEquals([1, 2, 3], $res);
	}

	public function testFetchOb(): void
	{
		$tmpl = 'tmpl.php';
		file_put_contents(static::$tmpdir . '/' . $tmpl, <<<__
<?php return function(): string { ob_start(); ?>
	<h1>A</h1>

<?php return trim(ob_get_clean()); };
__
		);

		$res = $this->View->fetch($tmpl)();
		$this->assertEquals('<h1>A</h1>', $res);
	}

	public function testFetchExceptionNothingFound(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Nothing found at 'MISSING'");
		$this->View->fetch('MISSING')();
	}

	public function testFetchExceptionClosureNotFound(): void
	{
		$tmpl = 'tmpl.php';
		file_put_contents(static::$tmpdir . '/' . $tmpl, <<<__
<?php return true;
__
		);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Closure not found at '$tmpl'");
		$this->View->fetch($tmpl)();
	}
}

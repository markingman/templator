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

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
		if (!static::tmpdir_make()) {
			throw new Exception('Could not create tmpdir');
		}
	}

	public static function tearDownAfterClass(): void
	{
		if (!static::tmpdir_remove()) {
			throw new Exception('Could not create tmpdir');
		}
	}

	public function setUp(): void
	{
		if (is_null(static::$tmpdir)) {
			throw new Exception(message: 'tmpdir not set');
		}

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

	public function testFetchNested(): void
	{
		$tmpl1 = 'tmpl1.php';
		$tmpl2 = 'tmpl2.php';

		file_put_contents(static::$tmpdir . '/' . $tmpl1, <<<__
<?php

use Templator\View;

return function(View \$View): string {
	return \$View->fetch('$tmpl2')('test');
};

__
		);

		file_put_contents(static::$tmpdir . '/' . $tmpl2, <<<__
<?php 
return function(string \$var): string {
	ob_start();

	if (\$var === 'test') { 
		?><p>test</p><?php 
	}

	return ob_get_clean();
};

__
		);
		$res = $this->View->fetch($tmpl1)($this->View);
		$this->assertEquals('<p>test</p>', $res);
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

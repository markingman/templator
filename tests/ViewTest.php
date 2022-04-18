<?php

use Templator\View;
use Templator\ViewInterface;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
	use TmpDirTestHelpersTrait;

	protected $View;

	public static function setUpBeforeClass(): void
	{
		static::tmpdir_make(self::class);

		file_put_contents(static::$tmpdir . '/string.php', <<<__
<?php

return 'Hello World';

__
		);

		file_put_contents(static::$tmpdir . '/array.php', <<<__
<?php

return ['a' => 1, 'b' => 2];

__
		);

		file_put_contents(static::$tmpdir . '/vars1.php', <<<__
<?php

if (@\$var === true) {
	return 'TRUE';
} else {
	return 'FALSE';
}

__
		);

		file_put_contents(static::$tmpdir . '/vars2.php', <<<__
<?php

return 10;

__
		);

		file_put_contents(static::$tmpdir . '/vars2.1.php', <<<__
<?php

return 1;

__
		);
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
		$res = $this->View->get($view);
		$this->assertTrue($res === 'Hello World');
	}

	public function testGetTemplateData(): void
	{
		$view = 'array.php';
		$res = $this->View->get($view);
		$this->assertTrue($res === ['a' => 1, 'b' => 2]);
	}

	public function testTemplateVars(): void
	{
		$view = 'vars1.php';
		$res = $this->View->get($view, ['var' => true]);
		$this->assertTrue($res === 'TRUE');
	}

	public function testTemplateVarsOb(): void
	{
		$view = 'vars2.php';
		$res = $this->View->get($view, null, false);
		$this->assertTrue($res === 10);

		$res = $this->View->get($view, null, true);
		$this->assertTrue($res === 10);

		$view = 'vars2.1.php';
		$res = $this->View->get($view, null, false);
		$this->assertTrue($res === 1);

		// assume ob doesn't work if result might be === 1
		$res = $this->View->get($view, null, true);
		$this->assertTrue($res === '');
	}

	public static function tearDownAfterClass(): void
	{
		static::tmpdir_remove();
	}
}

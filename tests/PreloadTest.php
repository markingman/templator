<?php

namespace Templator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class PreloadTest extends TestCase
{
	use TmpDirTestHelpersTrait;

	protected Preload $Preload;

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
		if (!is_string(static::$tmpdir) or !is_dir(static::$tmpdir) or !is_writable(static::$tmpdir)) {
			throw new RuntimeException('Expected writable tmp dir');
		}

		$this->Preload = new Preload(static::$tmpdir);
	}

	public function testCreate(): void
	{
		$this->assertInstanceOf(PreloadInterface::class, $this->Preload);
	}

	public function testCreateFailure(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Could not set path');
		new Preload('./--missing--');
	}

	public function testFind(): void
	{
		file_put_contents(static::$tmpdir . '/test1.php', '.');
		$this->assertFileExists(static::$tmpdir . '/test1.php');

		$this->assertEquals(
			static::$tmpdir . '/test1.php',
			$this->Preload->find('test1.php')
		);
	}

	public function testFindMissing(): void
	{
		$this->assertFalse($this->Preload->find('-MISSING-'));
	}

	public function testPreload(): void
	{
		$fn = 'test_' . uniqid();

		file_put_contents(
			static::$tmpdir . '/' . $fn . '.php',
			<<<EOF
<?php
namespace Templator\\preload;
function $fn(): string {
	return 'ok';
}
EOF
		);
		$this->assertFileExists(static::$tmpdir . '/' . $fn . '.php');

		$fn_name = "Templator\\preload\\$fn";
		$this->assertFalse(function_exists($fn_name));
		$this->Preload->preload($fn . '.php');
		$this->assertTrue(function_exists($fn_name));
		$this->assertEquals('ok', call_user_func($fn_name));
	}

	public function testPreloadMissing(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Nothing found at \'-MISSING-\'');

		$this->Preload->preload('-MISSING-');
	}

	public function testPreloadMultiple(): void
	{
		$fn = 'test_' . uniqid();

		file_put_contents(
			static::$tmpdir . '/' . $fn . '.php',
			<<<EOF
<?php
namespace Templator\\preload;
function $fn(): string {
	return 'ok';
}
EOF
		);
		$this->assertFileExists(static::$tmpdir . '/' . $fn . '.php');

		$fn_name = "Templator\\preload\\$fn";
		$this->assertFalse(function_exists($fn_name));

		$this->Preload->preload($fn . '.php');
		$this->assertTrue(function_exists($fn_name));
		$this->assertEquals('ok', call_user_func($fn_name));

		for ($i = 0; $i < 2; $i++) {
			$this->Preload->preload($fn . '.php');
		}
		$this->assertTrue(function_exists($fn_name));
		$this->assertEquals('ok', call_user_func($fn_name));
	}

	public function testPreloadWildcard(): void
	{
		$fn1 = 'test_' . uniqid();
		$fn2 = 'test_' . uniqid();
		$dir1 = 'dir_' . uniqid();
		$dir1a = 'dir_' . uniqid();
		$dir2 = 'dir_' . uniqid();
		mkdir(static::$tmpdir . '/' . $dir1);
		mkdir(static::$tmpdir . '/' . $dir1a);
		mkdir(static::$tmpdir . '/' . $dir1 . '/' . $dir2);

		file_put_contents(
			static::$tmpdir . '/' . $dir1 . '/' . $fn1 . '.php',
			<<<EOF
<?php
namespace Templator\\preload\\$dir1;
function $fn1(): string {
	return 'ok';
}
EOF
		);
		$this->assertFileExists(static::$tmpdir . '/' . $dir1 . '/' . $fn1 . '.php');

		file_put_contents(
			static::$tmpdir . '/' . $dir1 . '/' . $dir2 . '/' . $fn2 . '.php',
			<<<EOF
<?php
namespace Templator\\preload\\$dir1\\$dir2;
function $fn2(): string {
	return 'ok';
}
EOF
		);
		$this->assertFileExists(static::$tmpdir . '/' . $dir1 . '/' . $dir2 . '/' . $fn2 . '.php');

		$fn1_name = "Templator\\preload\\$dir1\\$fn1";
		$fn2_name = "Templator\\preload\\$dir1\\$dir2\\$fn2";

		$this->assertFalse(function_exists($fn1_name));
		$this->assertFalse(function_exists($fn2_name));

		$this->Preload->preload($dir1a . '/*');
		$this->assertFalse(function_exists($fn1_name));
		$this->assertFalse(function_exists($fn2_name));

		$this->Preload->preload($dir1 . '/*');
		$this->assertTrue(function_exists($fn1_name));
		$this->assertEquals('ok', call_user_func($fn1_name));
		$this->assertTrue(function_exists($fn2_name));
		$this->assertEquals('ok', call_user_func($fn2_name));
	}

	public function testPreloadDirMissing(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Nothing found at \'-MISSING-\'');

		$this->Preload->preload_dir('-MISSING-');
	}

	public function testPreloadDirIsFile(): void
	{
		$fn = 'test_' . uniqid();

		file_put_contents(
			static::$tmpdir . '/' . $fn . '.php',
			<<<EOF
<?php
namespace Templator\\preload;
function $fn(): string {
	return 'ok';
}
EOF
		);
		$this->assertFileExists(static::$tmpdir . '/' . $fn . '.php');

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Not a directory '$fn.php'");

		$this->Preload->preload_dir("$fn.php");
	}
}

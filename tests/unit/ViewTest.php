<?php

namespace Templator;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use UnexpectedValueException;

class ViewTest extends TestCase
{
	protected string $path_fixtures;
	protected View $View;

	public function setUp(): void
	{
		if (!$path_fixtures = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'fixtures')) {
			throw new RuntimeException('Could not find fixtures');
		}

		$this->path_fixtures = $path_fixtures;
		$this->View = new View($this->path_fixtures);
	}

	public function testCreate(): void
	{
		$this->assertInstanceOf(ViewInterface::class, $this->View);
	}

	public function testCreateFailure(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Could not set path');
		new View('./--missing--');
	}

	public function testFind(): void
	{
		$this->assertEquals($this->path_fixtures . '/fetch_simple.php', $this->View->find('fetch_simple.php'));
	}

	public function testFindMissing(): void
	{
		$this->assertFalse($this->View->find('-MISSING-'));
	}

	public function testFetchAndCall(): void
	{
		$this->assertEquals('abc', $this->View->fetch('fetch_string.php')());
	}

	public function testFetchAndCallWithArgs(): void
	{
		$this->assertEquals([1, 2, 3], $this->View->fetch('fetch_with_args.php')(true, 'B', [1]));
	}

	public function testFetchOb(): void
	{
		$this->assertEquals('<h1>A</h1>', $this->View->fetch('fetch_with_ob.php')());
	}

	public function testFetchNested(): void
	{
		$this->assertEquals('<p>test</p>', $this->View->fetch('fetch_nested1.php')($this->View));
	}

	public function testFetchExceptionNothingFound(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("Nothing found at 'MISSING'");
		$this->View->fetch('MISSING')();
	}

	public function testFetchExceptionClosureNotFound(): void
	{
		$this->expectException(UnexpectedValueException::class);
		$this->expectExceptionMessage("Closure not found at 'fetch_not_closure.php'");
		$this->View->fetch('fetch_not_closure.php')();
	}
}

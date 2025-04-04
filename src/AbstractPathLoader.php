<?php declare(strict_types=1);

namespace Templator;

use InvalidArgumentException;

abstract class AbstractPathLoader
{
	protected string $path;

	public function __construct(string $path)
	{
		if (!$path = realpath($path)) {
			throw new InvalidArgumentException("Could not set path");
		}

		$this->path = $path . DIRECTORY_SEPARATOR;
	}

	public function find(string $view): string|false
	{
		$file = realpath($this->path . $view);

		return ($file and is_file($file)) ? $file : false;
	}
}

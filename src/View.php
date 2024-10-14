<?php

namespace Templator;

use Closure;
use Exception;

class View implements ViewInterface
{
	protected string $path;

	public function __construct(string $path)
	{
		if (!$path = realpath($path)) {
			throw new Exception("Could not set path");
		}

		$this->path = $path . DIRECTORY_SEPARATOR;
	}

	public function find(string $view): string|false
	{
		if ($file = realpath($this->path . $view) and is_file($file)) {
			return $file;
		} else {
			return false;
		}
	}

	public function fetch(string $path): Closure
	{
		if (!$realpath = $this->find($path)) {
			throw new Exception("Nothing found at '$path'");
		}

		if (!($closure = include $realpath) instanceof Closure) {
			throw new Exception("Closure not found at '$path'");
		}

		return $closure;
	}
}

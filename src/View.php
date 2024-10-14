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

	public function get(string $view, array $_VARS = null, $assume_ob = true): mixed
	{
		// Deprecated

		if (($_TEMPLATE = $this->find($view)) === false) {
			return '';
		}

		$_VARS = (array)$_VARS;

		ob_start();

		$ret = call_user_func(
			function () use ($_TEMPLATE, $_VARS) {
				if ($_VARS) {
					extract($_VARS, EXTR_REFS);
				}

				return require $_TEMPLATE;
			}
		);

		$ob = (string)ob_get_clean();

		return ($ret === 1 and $assume_ob) ? $ob : $ret;
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

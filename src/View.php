<?php

namespace Templator;

use Exception;
use Closure;

class View implements ViewInterface
{
	protected string $path;

	public function __construct(string $path)
	{
		$this->path = realpath($path) . '/';
	}

	public function find(string $view): string|false
	{
		return realpath($this->path . $view);
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

<?php

namespace Templator;

class View implements ViewInterface
{
	protected $path;

	public function __construct(string $path)
	{
		$this->path = realpath($path) . '/';
	}

	public function find(string $view)
	{
		return realpath($this->path . $view);
	}

	public function get(string $view, array $_VARS = null, $assume_ob = true)
	{
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
}

<?php

namespace Templator;

use Closure;

interface ViewInterface
{
	public function find(string $view): string|false;

	public function get(string $view, array $_VARS = null): mixed;

	public function fetch(string $path): Closure;
}

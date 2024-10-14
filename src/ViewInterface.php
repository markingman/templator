<?php

namespace Templator;

use Closure;

interface ViewInterface
{
	public function find(string $view): string|false;

	public function fetch(string $path): Closure;
}

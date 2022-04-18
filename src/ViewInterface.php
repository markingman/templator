<?php

namespace Templator;

interface ViewInterface
{
	public function find(string $view): string|false;

	public function get(string $view, array $_VARS = null): mixed;
}

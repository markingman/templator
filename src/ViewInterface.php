<?php

namespace Templator;

interface ViewInterface
{
	public function find(string $view);

	public function get(string $view, array $_VARS = null);
}

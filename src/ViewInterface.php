<?php declare(strict_types=1);

namespace Templator;

use Closure;

interface ViewInterface
{
	public static function ob_start(): void;

	public static function ob_get_clean(): string;

	public function fetch(string $path): Closure;
}

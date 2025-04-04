<?php declare(strict_types=1);

namespace Templator;

interface PreloadInterface
{
	public function preload(string ...$templates): void;

	public function preload_dir(string $dir): void;
}

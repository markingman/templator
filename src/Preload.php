<?php declare(strict_types=1);

namespace Templator;

use Closure;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

class Preload extends AbstractPathLoader implements PreloadInterface
{
	public function preload(string ...$templates): void
	{
		foreach ($templates as $path) {
			if (str_ends_with($path, DIRECTORY_SEPARATOR . '*')) {
				$this->preload_dir(substr($path, 0, -2));
			} else {
				if ($realpath = $this->find($path)) {
					$this->require_once($realpath);
				} else {
					throw new InvalidArgumentException("Nothing found at '$path'");
				}
			}
		}
	}

	public function preload_dir(string $dir): void
	{
		if (!$path = realpath($this->path . DIRECTORY_SEPARATOR . $dir)) {
			throw new InvalidArgumentException("Nothing found at '$dir'");
		}

		if (!is_dir($path)) {
			throw new InvalidArgumentException("Not a directory '$dir'");
		}

		foreach (new RegexIterator(
					 new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)),
					 '/\.php$/'
				 ) as $template) {
			if ($template instanceof SplFileInfo) {
				$this->require_once($template->getPathname());
			}
		}
	}

	protected function require_once(string $path): void
	{
		(Closure::bind(static function ($path): void {
			require_once $path;
		}, null, null))($path);
	}
}

<?php declare(strict_types=1);

namespace Templator;

use Closure;
use InvalidArgumentException;
use UnexpectedValueException;

class View extends AbstractPathLoader implements ViewInterface
{
	public static function ob_start(): void
	{
		ob_start();
	}

	public static function ob_get_clean(): string
	{
		return strval(ob_get_clean());
	}

	public function fetch(string $path): Closure
	{
		if (!$realpath = $this->find($path)) {
			throw new InvalidArgumentException("Nothing found at '$path'");
		}

		if (!(
			$closure = (Closure::bind(static function ($path): mixed {
				return include $path;
			}, null, null))($realpath)
			) instanceof Closure) {
			throw new UnexpectedValueException("Closure not found at '$path'");
		}

		return $closure;
	}
}

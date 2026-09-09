<?php declare(strict_types=1);

namespace Templator;

use LogicException;

class ViewHTML extends View implements ViewHTMLInterface
{
	const int ENT_ENTITIES = ENT_QUOTES | ENT_HTML401;
	const int ENT_CHARS = ENT_NOQUOTES | ENT_HTML401;
	private const string MARKER_OPEN = "\x02";
	private const string MARKER_CLOSE = "\x03";

	public static function htmlentities(string $s): string
	{
		return htmlentities($s, flags: static::ENT_ENTITIES, double_encode: false);
	}

	/** @param array<int, string> $t */
	public static function htmlspecialchars(string $s, ?array $t = null, bool $strip = true): string
	{
		if (!str_contains($s, '<')) {
			return htmlspecialchars($s, flags: static::ENT_CHARS, double_encode: false);
		}

		if ($t) {
			if (str_contains($s, self::MARKER_OPEN)) {
				$s = str_replace(self::MARKER_OPEN, '', $s);
			}

			if (str_contains($s, self::MARKER_CLOSE)) {
				$s = str_replace(self::MARKER_CLOSE, '', $s);
			}

			if ($strip) {
				$s = strip_tags($s, $t);
			}

			$s = @preg_replace(
				'~<' . '(/*(' . implode('|', $t) . ')( [^>]*)*)' . '>~', self::MARKER_OPEN . '$1' . self::MARKER_CLOSE, $s
			);

			if ($s === null || preg_last_error() !== PREG_NO_ERROR) {
				throw new LogicException('Could not replace tags due to preg_replace error: ' . preg_last_error());
			}
		} elseif ($strip) {
			$s = strip_tags($s);
		}

		$s = htmlspecialchars($s, flags: static::ENT_CHARS, double_encode: false);

		if ($t) {
			$s = str_replace([self::MARKER_OPEN, self::MARKER_CLOSE], ['<', '>'], $s);
		}

		return $s;
	}

	/**
	 * @param array<string, int|string|bool|null> $atts
	 * @param array<string> $mask
	 */
	public static function htmlatts(array $atts = [], array $mask = []): string
	{
		if ($mask) {
			$atts = array_diff_key($atts, array_flip($mask));
		}

		$ret = '';
		foreach ($atts as $k => $v) {
			if (is_bool($v)) {
				if ($v) {
					$ret .= $k . ' ';
				}
			} elseif (is_string($v) or is_int($v)) {
				$ret .= $k . '="' . static::htmlentities((string)$v) . '" ';
			}
		}

		return substr($ret, 0, -1);
	}

	/** @param array<string, int|string|bool|null> $atts */
	public static function tag(string $tag, ?array $atts = null, string $html = ''): string
	{
		// e.g:
		// tag('img/', ['src' => 'a.jpg']) 
		// tag('a', ['href' => $var, 'class' => 'btn', 'onclick' => 'fc()'])
		// tag('p/', null, 'Hello, <b>world</b>')
		// tag('/div')
		// tag('script/', ['src' => 'script.js'], PHP_EOL)

		if (str_starts_with($tag, '/')) {
			return '<' . $tag . '>';
		}

		if ($close = str_ends_with($tag, '/')) {
			$tag = rtrim($tag, '/');
		}

		$s = '<' . $tag;

		if ($atts) {
			$s .= ' ' . static::htmlatts($atts);
		}

		if ($html !== '') {
			$s .= '>' . $html;
			if ($close) {
				$s .= '</' . $tag . '>';
			}

			return $s;
		}

		if ($close) {
			$s .= ' /';
		}

		$s .= '>';

		return $s;
	}
}

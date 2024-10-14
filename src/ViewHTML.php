<?php

namespace Templator;

class ViewHTML extends View implements ViewHTMLInterface
{
	const ENT_ENTITIES = ENT_QUOTES | ENT_HTML5;
	const ENT_CHARS = ENT_NOQUOTES | ENT_HTML5;

	public static function htmlentities(string $s): string
	{
		return htmlentities($s, flags: static::ENT_ENTITIES, double_encode: false);
	}

	public static function htmlspecialchars(string $s, ?array $t = null, bool $strip = true): string
	{
		static $o = "\x02", $c = "\x03";

		if (!str_contains($s, '<')) {
			return htmlspecialchars($s, flags: static::ENT_CHARS, double_encode: false);
		}

		if ($t) {
			if (str_contains($s, $o)) {
				$s = str_replace($o, '', $s);
			}

			if (str_contains($s, $c)) {
				$s = str_replace($c, '', $s);
			}

			if ($strip) {
				$s = strip_tags($s, '<' . implode('><', $t) . '>');
			}

			$s = preg_replace(
				'~' . '<' . '(/*(' . implode('|', $t) . ')( [^>]*)*)' . '>' . '~',
				$o . '$1' . $c, $s
			);
		} elseif ($strip) {
			$s = strip_tags($s);
		}

		$s = htmlspecialchars($s, flags: static::ENT_CHARS, double_encode: false);

		if ($t) {
			$s = str_replace([$o, $c], ['<', '>'], $s);
		}

		return $s;
	}

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
			} elseif (!is_null($v)) {
				$ret .= $k . '="' . static::htmlentities($v) . '" ';
			}
		}

		return substr($ret, 0, -1);
	}

	public static function tag(string $tag, array $atts = null, string $html = ''): string
	{
		// e.g:
		// tag('img/', ['src' => 'a.jpg']) 
		// tag('a', ['href' => $var, 'class' => 'btn', 'onclick' => 'fc()'])
		// tag('p/', null, 'Hello, <b>world</b>')
		// tag('/div')
		// tag('script/', ['src' => 'script.js'], PHP_EOL)

		if ($close = boolval(strpos($tag, '/'))) {
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

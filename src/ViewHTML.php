<?php

namespace Templator;

class ViewHTML extends View implements ViewHTMLInterface
{
	const ENT_HTML = ENT_HTML5;

	public function htmlentities(string $s): string
	{
		return htmlentities($s, ENT_QUOTES | static::ENT_HTML, null, false);
	}

	public function htmlspecialchars(string $s, array $t = null, bool $strip = true): string
	{
		if (!str_contains($s, '<')) {
			return htmlspecialchars($s, ENT_NOQUOTES | static::ENT_HTML, null, false);
		}

		if ($t) {
			$o = chr(2);
			$c = chr(3);

			if (str_contains($s, $o)) {
				$s = str_replace([$o, $c], '', $s);
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

		$s = htmlspecialchars($s, ENT_NOQUOTES | ENT_HTML5, null, false);

		if ($t) {
			$s = str_replace([$o, $c], ['<', '>'], $s);
		}

		return $s;
	}

	public function htmlatts(array $atts = [], array $mask = []): string
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
				$ret .= $k . '="' . $this->htmlentities($v) . '" ';
			}
		}

		return substr($ret, 0, -1);
	}

	public function tag(string $tag, array $atts = null, string $html = ''): string
	{
		// e.g:
		// tag('img/', ['src' => 'a.jpg']) 
		// tag('a', ['href' => $var, 'class' => 'btn', 'onclick' => 'fc()']);
		// tag('p/', null, 'Hello, <b>world</b>')
		// tag('/div')

		if ($close = strpos($tag, '/')) {
			$tag = rtrim($tag, '/');
		}
		$s = '<' . $tag;
		if ($atts) {
			$s .= ' ' . $this->htmlatts($atts);
		}
		if ($html) {
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

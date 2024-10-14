<?php

namespace Templator;

interface ViewHTMLInterface
{
	public static function htmlentities(string $s): string;

	public static function htmlspecialchars(string $s, array $t = null, bool $strip = true): string;

	public static function htmlatts(array $atts = [], array $mask = []): string;

	public static function tag(string $tag, array $atts = null, string $html = ''): string;
}

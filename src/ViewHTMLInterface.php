<?php

namespace Templator;

interface ViewHTMLInterface
{
	public static function htmlentities(string $s): string;

	/** @param array<string> $t */
	public static function htmlspecialchars(string $s, array $t = null, bool $strip = true): string;

	/** 
	 * @param array<string, string|bool> $atts
	 * @param array<string> $mask
	 */
	public static function htmlatts(array $atts = [], array $mask = []): string;

	/** @param array<string, string|bool> $atts */
	public static function tag(string $tag, array $atts = null, string $html = ''): string;
}

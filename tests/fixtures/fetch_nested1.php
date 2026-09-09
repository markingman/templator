<?php

use Templator\View;

return function (View $View): string {
	$ret = $View->fetch('fetch_nested2.php')('test');

	return is_string($ret) ? $ret : '';
};

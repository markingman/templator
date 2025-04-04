<?php

use Templator\View;

return function (View $View): string {
	return $View->fetch('fetch_nested2.php')('test');
};

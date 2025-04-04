<?php

use Templator\View;

return function (string $var): string {
	View::ob_start();

	if ($var === 'test') {
		?><p>test</p><?php
	}

	return View::ob_get_clean();
};


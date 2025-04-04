<?php

use Templator\View;

return function (): string {
	View::ob_start();
	?>

	<h1>A</h1>

	<?php
	return trim(View::ob_get_clean());
};

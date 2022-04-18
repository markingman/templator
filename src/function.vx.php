<?php

// Print variables and exit dev function
// Add file name composer autoload-dev.files

function vx(...$vs)
{
	while (ob_get_length()) {
		ob_end_clean();
	}

	if (strpos(PHP_SAPI, 'cli') === false) {
		header('Content-Type: text/plain; charset=UTF-8');
	}

	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	echo $b[0]['file'] . ':' . $b[0]['line'], PHP_EOL, PHP_EOL;

	foreach ($vs as $v) {
		var_export($v);
		echo PHP_EOL . PHP_EOL;
	}

	echo PHP_EOL;

	exit;
}

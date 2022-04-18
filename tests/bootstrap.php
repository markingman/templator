<?php

$dir = __DIR__;
require_once $dir . '/TmpDirTestHelpersTrait.php';

while ($dir !== '/' and !is_file($dir . '/vendor/autoload.php')) {
	$dir = dirname($dir);
}

require_once $dir . '/vendor/autoload.php';

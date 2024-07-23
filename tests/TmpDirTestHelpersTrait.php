<?php

namespace Templator;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait TmpDirTestHelpersTrait
{
	protected static ?string $tmpdir = null;

	protected static function tmpdir_make(): bool
	{
		static::$tmpdir = rtrim(sys_get_temp_dir(), '/') . '/' . bin2hex(random_bytes(4));

		return file_exists(static::$tmpdir) ? false : mkdir(static::$tmpdir, 0755, true);
	}

	protected static function tmpdir_remove(): bool
	{
		if (!is_null(static::$tmpdir)) {
			return false;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(static::$tmpdir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $filename => $fileInfo) {
			$fileInfo->isDir() ? rmdir($filename) : unlink($filename);
		}

		return rmdir(static::$tmpdir);
	}
}

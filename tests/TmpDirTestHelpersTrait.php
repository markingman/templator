<?php

namespace Templator;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use SplFileInfo;

trait TmpDirTestHelpersTrait
{
	protected static ?string $tmpdir = null;

	protected static function tmpdir_make(): bool
	{
		try {
			static::$tmpdir = rtrim(sys_get_temp_dir(), '/') . '/' . bin2hex(random_bytes(4));
		} catch (Exception $e) {
			throw new Exception(message: 'Could not create tmpdir; ' . $e->getMessage());
		}

		if (!mkdir(static::$tmpdir, 0755, true)) {
			throw new Exception(message: 'Could not create tmpdir');
		}

		return true;
	}

	protected static function tmpdir_remove(): bool
	{
		if (is_null(static::$tmpdir)) {
			return false;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(static::$tmpdir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file => $info) {
			if (is_string($file) and $info instanceof SplFileInfo) {
				$info->isDir() ? rmdir($file) : unlink($file);
			}
		}

		if (!rmdir(static::$tmpdir)) {
			throw new Exception(message: 'Could not remove tmpdir');
		}

		return true;
	}
}

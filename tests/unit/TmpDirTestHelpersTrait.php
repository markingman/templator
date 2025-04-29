<?php

namespace Templator;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Throwable;

trait TmpDirTestHelpersTrait
{
	protected static ?string $tmpdir = null;

	protected static function tmpdir_make(): bool
	{
		if (!is_writable(sys_get_temp_dir())) {
			throw new RuntimeException('Cannot write to tmp dir');
		}

		try {
			static::$tmpdir = rtrim(sys_get_temp_dir(), '/') . '/' . bin2hex(random_bytes(4));
		} catch (Throwable $e) {
			throw new RuntimeException(message: 'Could not create tmp dir; ' . $e->getMessage());
		}

		if (!mkdir(static::$tmpdir, 0755, true)) {
			throw new RuntimeException('Could not create tmp dir');
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
			throw new RuntimeException('Could not remove tmpdir');
		}

		return true;
	}
}

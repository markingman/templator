<?php

// Simple server to load test pages

namespace Templator;

use View;

class DevServer
{
	protected string $dir;
	protected array $routes;
	protected ?string $path404 = null;

	public function __construct(string $dir, array $routes = null)
	{
		$this->dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$this->routes = $routes ?: [];
	}

	public function setPath404(string $path): void
	{
		$this->path404 = $path;
	}

	public function request(string $path, ViewInterface $View): void
	{
		if (!$this->routes) {
			http_response_code(500);
			exit(sprintf('Could not load routes (%s)', strtolower(json_last_error_msg())));
		} elseif (!isset($this->routes[$path])) {
			http_response_code(404);
			if ($this->path404 and file_exists($this->dir . $this->path404)) {
				include $page;
				exit;
			} else {
				exit('No route found.');
			}
		} elseif (
			!is_string($this->routes[$path]) or empty($this->routes[$path])
			or !file_exists($page = $this->dir . $this->routes[$path])
		) {
			http_response_code(500);
			exit('Could not load page.');
		} else {
			include $page;
			exit;
		}
	}
}

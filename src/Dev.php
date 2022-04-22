<?php

namespace Templator;

use Exception as Exception;

use JShrink\Minifier as JavaScriptMinifier;

class Dev
{
	protected string $tmpl_dir;
	protected string $http_dir;

	public function __construct(string $tmpl_dir, string $http_dir)
	{
		$http_dir = realpath($http_dir);
		$tmpl_dir = realpath($tmpl_dir);

		foreach (['tmpl_dir', 'http_dir'] as $d) {
			if ($$d === false or !is_dir($$d) or !is_readable($$d)) {
				throw new Exception(sprintf('Could not read %s', $d) . $e->getMessage());
			}
		}

		$this->http_dir = $http_dir;
		$this->tmpl_dir = $tmpl_dir;

		return $this;
	}

	public function makeDevServer(): void
	{
		$here = __DIR__;
		$http_dir = $this->http_dir . DIRECTORY_SEPARATOR;
		$tmpl_dir = $this->tmpl_dir . DIRECTORY_SEPARATOR;
		if (strpos($here, $http_dir) === 0) {
			$here = substr($here, strlen($http_dir));
		}
		if (strpos($tmpl_dir, $http_dir) === 0) {
			$tmpl_dir = rtrim(substr($tmpl_dir, strlen($http_dir)), DIRECTORY_SEPARATOR);
		}
		$namespace = __NAMESPACE__;

		try {
			file_put_contents(
				$this->http_dir . '/.htaccess',
				<<<__
RewriteEngine On
RewriteRule favicon\.ico {$tmpl_dir}/img/favicon.ico
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* index.php
__
			);
		} catch (Exception $e) {
			throw new Exception('Could not create .htaccess file; ' . $e->getMessage());
		}

		try {
			file_put_contents(
				$this->http_dir . '/index.php',
				<<<__
<?php

// Test front page controller, type over to suite
// Remember this is just to load template/dev/pages/ test files and not for production!

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/error.log');

spl_autoload_register(function (\$class) {
	\$f = sprintf('$here/%s.php', str_replace('$namespace\\\\', '', \$class));
	if (is_readable(\$f)) {
		include \$f;
	}
});

include '$here/function.vx.php';

\$routes = json_decode(@file_get_contents('{$tmpl_dir}/dev/routes.json'), true);
\$pages = realpath('{$tmpl_dir}/dev/pages');
\$dir = '{$tmpl_dir}';
\$path = '/' . trim(parse_url((string)@\$_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
\$config = @include '{$tmpl_dir}/dev/config.php';

try {
	\$DevServer = new $namespace\DevServer(\$pages, \$routes);
	\$View = new $namespace\View(\$dir);
	if (\$config) {
		foreach (\$config as \$k => \$v) {
			\$View->\$k = \$v;
		}
	}
} catch (Exception \$e) {
	http_response_code(500);
	exit(sprintf('Could not load server (%s)', \$e->getMessage()));
}

\$DevServer->request(\$path, \$View);
__
			);
		} catch (Exception $e) {
			throw new Exception('Could not create index.php file; ' . $e->getMessage());
		}
	}

	public function makeExample(): void
	{
		try {
			if (!file_exists($this->tmpl_dir . '/.htaccess')) {
				file_put_contents(
					$this->tmpl_dir . '/.htaccess',
					<<<__
Options -Indexes

RewriteEngine On

RewriteRule \.php$ - [R=404,L]
RewriteRule \.scss$ - [R=404,L]
RewriteRule ^composer\. - [R=404,L]
RewriteRule ^.git - [R=404,L]
RewriteRule ^.md - [R=404,L]
RewriteRule ^vendor/ - [R=404,L]

RewriteRule ^css/[0-9]*/(.+).css$ css/$1.css [L]
RewriteRule ^js/[0-9]*/(.+).js$ js/$1.js [L]
RewriteRule ^img/[0-9]*/(.+)$ img/$1 [L]
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create .htaccess file; ' . $e->getMessage());
		}

		try {
			if (!file_exists($this->tmpl_dir . '/.gitignore')) {
				file_put_contents(
					$this->tmpl_dir . '/.gitignore',
					<<<__
.DS_Store
/tmp
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create .gitignore file; ' . $e->getMessage());
		}

		try {
			if (!file_exists($this->tmpl_dir . '/README.md')) {
				file_put_contents(
					$this->tmpl_dir . '/README.md',
					<<<__
# Templates Project

Templates project.
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create README.md file; ' . $e->getMessage());
		}

		try {
			if (!file_exists($this->tmpl_dir . '/composer.json')) {
				file_put_contents(
					$this->tmpl_dir . '/composer.json',
					<<<__
{
	"name": "Templates Project",
	"config": {
        "vendor-dir": "vendor"
    },
    "require": {
        "php": ">=8"
    }
}
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create composer.json file; ' . $e->getMessage());
		}

		foreach (['js', 'css', 'img'] as $d) {
			try {
				if (!is_dir($this->tmpl_dir . DIRECTORY_SEPARATOR . $d)) {
					mkdir($this->tmpl_dir . DIRECTORY_SEPARATOR . $d);
				}
			} catch (Exception $e) {
				throw new Exception(sprintf('Could not create %s directory' . $e->getMessage(), $d));
			}
		}

		try {
			if (!file_exists($this->tmpl_dir . '/js/app.js.php')) {
				file_put_contents(
					$this->tmpl_dir . '/js/app.js.php',
					<<<__
<?php return [
	// '../vendor/example/dist/scripts.js',
	// 'example.js',
];
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create app.js.php file; ' . $e->getMessage());
		}

		try {
			if (!file_exists($this->tmpl_dir . '/css/styles.css.php')) {
				file_put_contents(
					$this->tmpl_dir . '/css/styles.css.php',
					<<<__
<?php return [
// '../vendor/example/dist/scripts.css',
// 'main.css',
];
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create styles.css.php file; ' . $e->getMessage());
		}

		try {
			if (!file_exists($this->tmpl_dir . '/css/main.scss')) {
				file_put_contents(
					$this->tmpl_dir . '/css/main.scss',
					<<<__
//
__
				);
			}
		} catch (Exception $e) {
			throw new Exception('Could not create main.scss file; ' . $e->getMessage());
		}
	}

	public function makeDevExample(): void
	{
		foreach (['dev', 'dev/pages', 'dev/data', 'dev/assets'] as $d) {
			if (!is_dir($this->tmpl_dir . DIRECTORY_SEPARATOR . $d)) {
				mkdir($this->tmpl_dir . DIRECTORY_SEPARATOR . $d);
			}
		}

		if (!file_exists($this->tmpl_dir . '/dev/routes.json')) {
			file_put_contents(
				$this->tmpl_dir . '/dev/routes.json',
				json_encode(['/' => 'index.php'], JSON_PRETTY_PRINT)
			);
		}

		if (!file_exists($this->tmpl_dir . '/dev/config.php')) {
			file_put_contents(
				$this->tmpl_dir . '/dev/config.php',
				<<<__
<?php

\$d = '/' . basename(realpath(__DIR__ . '/../'));

return [
	'path_js' => \$d . '/js',// e.g. [cdn]/templates/js/123/
	'path_css' => \$d . '/css',
	'path_img' => \$d . '/img',
	'path_img_local' => \$d . '/img',//e.g. .svg 
	'env' => 'dev'
];
__
			);
		}

		if (!file_exists($this->tmpl_dir . '/dev/pages/index.php')) {
			file_put_contents(
				$this->tmpl_dir . '/dev/pages/index.php',
				<<<__
<?php

// Get test data like this:
// \$data = \$View->get(dev/data/test.php);

// Call a view template like this:
// \$content = \$View->get(example.php, \$data);

// Combine templates like this:
// echo \$View->get(template.php, ['content' => $content]);

// See tmpl.valhalla.software for more examples.

echo '<p>Test</p>';
__
			);
		}
	}

	public function makeCSS(string $path = 'css', bool $min = false): void
	{
		$dir = $this->tmpl_dir . DIRECTORY_SEPARATOR . trim($path, '/\\');

		foreach (glob($dir . '/*.css.php') as $file) {
			$files = include $file;
			$file = dirname($file) . '/' . basename($file, '.php');
			$tmp = tempnam(dirname($file), basename($file));

			foreach ($files as $css) {
				if (!file_exists($css)) {
					$css = $dir . DIRECTORY_SEPARATOR . $css;
				}
				if (!file_exists($css)) {
					throw new Exception(sprintf('Could not find file %s', $css));
				}
				file_put_contents($tmp, file_get_contents($css) . PHP_EOL . PHP_EOL, FILE_APPEND);
			}

			rename($tmp, $file);
			chmod($file, 0644);
		}

		if ($min) {
			foreach (glob($dir . '/*.css') as $file) {
				$css = file_get_contents($file);

				$css = str_replace('/*', '<<<', $css);
				$css = str_replace('*/', '>>>', $css);
				$css = preg_replace('/<<<.*?>>>/s', '', $css);

				$css = preg_replace('~\s*([\{\};:,])\s*~', '$1', $css);
				$css = preg_replace('~\s*([\+\>])\s*~', '$1', $css);

				$css = preg_replace('/\+([0-9])/', ' + $1', $css);//was failing in calc(...+...)

				$css = trim($css);

				file_put_contents($file, $css);
				chmod($file, 0644);
			}
		}
	}

	public function makeJS(string $path = 'js', bool $min = false, bool $nomap = false): void
	{
		$dir = rtrim($this->tmpl_dir . trim($path, '/'), '/');

		foreach (glob($dir . '/*.js.php') as $file) {
			$files = include $file;
			$file = dirname($file) . '/' . basename($file, '.php');
			$tmp = tempnam(dirname($file), basename($file));

			foreach ($files as $js) {
				if (!file_exists($js)) {
					$js = $dir . DIRECTORY_SEPARATOR . $js;
				}
				if (!file_exists($js)) {
					throw new Exception(sprintf('Could not find file %s', $js));
				}
				$c = file_get_contents($js);
				if ($nomap) {
					$c = preg_replace("~//# sourceMappingURL.+\n~", '', $c);
				}
				file_put_contents($tmp, $c . PHP_EOL . PHP_EOL, FILE_APPEND);
			}

			if ($min) {
				$this->minifyJS($tmp);
			}

			rename($tmp, $file);
			chmod($file, 0644);
		}
	}

	protected function minifyJS(string $path): void
	{
// 		if (
// 			is_callable([JavaScriptMinifier::class, 'minify'])
// 			or 
// 		) {

		if (@include $vendor . '/tedivm/jshrink/src/JShrink/Minifier.php') {
			file_put_contents($tmp, JShrink\Minifier::minify(file_get_contents($tmp), ['flaggedComments' => false]));
		}
	}
}

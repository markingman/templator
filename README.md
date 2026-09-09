# templator

A lightweight PHP server-side template system.

## Status

This is a small utility library shared for convenience. Maintenance is best-effort and may be minimal.

This repository is published for use and reference. External contributions are not currently being accepted

## Installation

To use, require in `composer.json`, e.g:

```bash
composer require markingman/templator
```

## Usage Overview

Templates are set up as PHP code.

Templates can be closures called by the `fetch()` method, or as namespace functions that can be preloaded with the `Preload` class.

Alternatively, templates could be from class methods and leverage PHP's class autoloading.

For example, a closure template:

```php
use Templator\ViewHTML;

return function(string $var, bool $val = true): string {
	ViewHTML::ob_start();
	?>

		<?php if ($val) { ?>
			<p><?= ViewHTML::htmlspecialchars($var) ?></p>	
		<?php } ?>

		<?= ViewHTML::tag('hr/', ['class' => 'rule']) ?>

	<?php
	return ViewHTML::ob_get_clean();
}
```

Using the pre-loader:

```php
$preload = new Preload('/templates/path');
$preload->preload([
	'Template/default_template.php',
	'Partials/*',
]);
```

## License

This project is licensed under the MIT License.

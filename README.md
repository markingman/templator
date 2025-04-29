# templator

Simple PHP server-side template system.

## Installation

To use, require in `composer.json`, e.g:

```
composer require markingman/templator
```

## Usage

Templates are set up as PHP code.

Templates can be closures called by the `fetch()` method, or as namespace functions that can be preloaded with the `Preload` class.

Alternatively, templates could be from class methods and leverage PHP's class autoloading.

For example, a closure template.

```php
<?php

use Templator\ViewHTML as View;

return function(string $var, bool $val = true): string {
	ob_start();
	?>

		<?php if ($val) { ?>
			<p><?= View::htmlspecialchars($var) ?></p>	
		<?php } ?>

		<?= View::tag('hr/', ['class' => 'rule']) ?>

	<?php
	return ob_get_clean();
}
```

# templator

Simple PHP server-side template system.

## Usage

To use, require in `composer.json`, e.g:

    "require": {  
        "markingman/templator": "dev-main#[TAG]",
    }

Templates are set up as closures with typed arguments. For example:

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

## Development

Run `make` for list of options.
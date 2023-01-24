# templator

Simple PHP server-side template system

## Usage

To use, require in `composer.json`, e.g:

    "require": {  
        "markingman/templator": "dev-main#[TAG]",
    }

## Development

For development `cd` into this directory.

Run `make setup` to get Composer dependencies.

Run `make start` to create a Docker container (with current PHP version).

Run `make ssh` to access container.

Inside container run `vendor/phpunit/phpunit/phpunit tests` (see details in `./phpunit.xml`).

Exit container with `exit` command.

Run `makde stop` to stop Docker container.

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

Run `make tests` to run tests in Docker container.

Run `make stop` to stop Docker container.

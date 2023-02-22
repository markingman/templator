# Makefile for local development

.DEFAULT_GOAL := help

.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ": ## "}; {printf "\033[36m%-28s\033[0m %s\n", $$1, $$2}' | sed 's/Makefile://g'

setup: ## Set up composer
	@composer install

start: ## Start up Docker container to run tests
	@docker run -d -p 888:80 --name templator-test -v "$(shell pwd)":/var/www/html php:8.1-apache

stop: ## Stop current containers
	@docker stop templator-test

test: ## Run tests in Docker container
	@docker exec -it templator-test vendor/phpunit/phpunit/phpunit

ssh: ## SSH to Docker container
	@docker exec -it templator-test sh

clean: ## Clean up
	@rm -Rf vendor .phpunit.result.cache

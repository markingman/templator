# Makefile for local development

.DEFAULT_GOAL := help

.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ": ## "}; {printf "\033[36m%-28s\033[0m %s\n", $$1, $$2}' | sed 's/Makefile://g'

build: ## Build and start Docker container to run tests
	@docker run -d -p 888:80 --platform linux/amd64 --name templator-test -v "$(shell pwd)":/var/www/html php:8.1-apache

setup: ## Set up environment in Docker container
	@docker exec -it templator-test curl https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet
	@docker exec -it templator-test php composer.phar install

install: ## Install composer in Container
	@docker exec -it templator-test php composer.phar install

update: ## Update composer in Container
	@docker exec -it templator-test php composer.phar update

start: ## Start up Docker container to run tests (if container has been built and stopped)
	@docker container start templator-test

stop: ## Stop current container (if running)
	@docker stop templator-test

test: ## Run tests in Docker container
	@docker exec -it templator-test vendor/phpunit/phpunit/phpunit

ssh: ## SSH to Docker container
	@docker exec -it templator-test sh

clean: ## Clean up
	@docker stop templator-test
	@rm -Rf vendor .phpunit.result.cache composer.phar
	@docker rm templator-test

# Makefile for local development

.DEFAULT_GOAL := help
.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ": ## "}; {printf "\033[36m%-28s\033[0m %s\n", $$1, $$2}' | sed 's/Makefile://g'

build: ## Build a Docker image for local development
	@docker build -t templator-test .

run: ## Run the Docker image
	@docker run -d -v `pwd`:/var/www/html --name templator-test templator-test

setup: ## Set up environment in Docker container
	@docker exec -it templator-test curl https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet
	@docker exec -it templator-test php composer.phar install

install: ## Install composer in Container
	@docker exec -it templator-test php composer.phar install

update: ## Update composer in Container
	@docker exec -it templator-test php composer.phar update

start: ## Start Docker container to run tests (if container built and stopped)
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

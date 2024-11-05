# Makefile for local development

.DEFAULT_GOAL := help
.PHONY: help
NAME=templator-test

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ": ## "}; {printf "\033[36m%-28s\033[0m %s\n", $$1, $$2}' | sed 's/Makefile://g'

build: ## Build a Docker image for local development
	@docker build -t $(NAME) .

test: ## Start container to run tests
	@docker run -it --rm -v `pwd`/src:/usr/src/myapp/src -v `pwd`/tests:/usr/src/myapp/tests -v `pwd`/phpunit-coverage:/usr/src/myapp/phpunit-coverage $(NAME) vendor/bin/phpunit

analyse: ## Start container to run analyse
	@docker run -it --rm -v `pwd`/src:/usr/src/myapp/src -v `pwd`/tests:/usr/src/myapp/tests $(NAME) vendor/bin/phpstan analyse -c phpstan.neon

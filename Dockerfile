FROM php:8.3-cli

RUN apt-get update && apt-get install -y zip
RUN pecl install xdebug-3.3.2 \
	&& docker-php-ext-enable xdebug

COPY --from=composer:2.8.1 /usr/bin/composer /usr/bin/composer

COPY . /usr/src/app
WORKDIR /usr/src/app
RUN /usr/bin/composer install

ENV XDEBUG_MODE=coverage

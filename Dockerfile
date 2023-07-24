FROM php:8.2-apache
VOLUME /var/www/html

RUN apt-get update && apt-get install -y unzip p7zip

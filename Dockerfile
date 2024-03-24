VOLUME /var/www/html
FROM php:8.3-apache

RUN apt-get update && apt-get install -y unzip p7zip

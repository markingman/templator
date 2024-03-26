FROM php:8.3-apache

RUN apt-get update && apt-get install -y unzip p7zip ssl-cert

RUN a2enmod rewrite ssl headers expires
RUN a2ensite default-ssl.conf

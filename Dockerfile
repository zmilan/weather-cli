FROM php:7.4-cli
WORKDIR /weather_cli

RUN apt-get update && apt-get install git curl zip unzip -y
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

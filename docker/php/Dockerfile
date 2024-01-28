FROM php:8.2-cli
WORKDIR /weather_cli

RUN apt-get update && apt-get install git curl zip unzip -y
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

ARG UID
ARG GID

RUN groupadd -g "${GID}" weather \
  && useradd --create-home --no-log-init -u "${UID}" -g "${GID}" weather

USER weather

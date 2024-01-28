---
# Weather PHP CLI Application

The Weather is a **PHP-based command-line app** that prints the current weather of any city which you specify as an argument.

## Installation requirements
- [git](https://git-scm.com/downloads)
- [docker](https://docs.docker.com/desktop/)
- [docker-compose](https://docs.docker.com/compose/install/)

## Installation
- Clone this repository with `git clone https://github.com/zmilan/weather-cli`
- Copy `.env.example` into `.env` and adjust variables. Required variables are:
    + `OPEN_WEATHER_MAP_URL` - OpenWeatherMap API url
    + `OPEN_WEATHER_MAP_KEY` - OpenWeatherMap API key (appid) value
    
    
## Starting application
- Run `docker-compose up -d && docker-compose exec weather_cli bash`

## Using application
- `./weather [city] [country]`. Examples:
    + Only with the city name: `./weather London` or `php weather London`
    + With the city and the country name: `./weather Trstenik SI` or `php weather Trstenik SI`

## Running application tests
- Execute `./vendor/bin/phpunit`
<?php


namespace Weather\Api;

use PHPUnit\Framework\TestCase;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiRequestException;

class OpenWeatherMapTest extends TestCase
{
    public function testGetDataSuccessfully(): void
    {
        $openWeatherMap = new OpenWeatherMap(
            $_ENV['OPEN_WEATHER_MAP_URL'],
            $_ENV['OPEN_WEATHER_MAP_KEY'],
            $_ENV['OPEN_WEATHER_MAP_UNITS']
        );
        $data = $openWeatherMap->getData('Kranj, SI');
        self::assertEquals($data['name'], 'Kranj');
    }

    public function testGetDataWrongConfig(): void
    {
        $this->expectException(WeatherApiRequestException::class);
        $openWeatherMap = new OpenWeatherMap(
            '',
            ''
        );
        $openWeatherMap->getData('Kranj, SI');
    }

    public function testGetDataMissingApiKey(): void
    {
        $this->expectException(WeatherApiRequestException::class);
        $openWeatherMap = new OpenWeatherMap(
            $_ENV['OPEN_WEATHER_MAP_URL'],
            ''
        );
        $openWeatherMap->getData('Kranj, SI');
    }

    public function testGetDataMissingCity(): void
    {
        $this->expectException(WeatherApiRequestException::class);
        $openWeatherMap = new OpenWeatherMap(
            $_ENV['OPEN_WEATHER_MAP_URL'],
            $_ENV['OPEN_WEATHER_MAP_KEY']
        );
        $openWeatherMap->getData('');
    }

    public function testGetDataOzCity(): void
    {
        $this->expectException(WeatherApiDataException::class);
        $openWeatherMap = new OpenWeatherMap(
            $_ENV['OPEN_WEATHER_MAP_URL'],
            $_ENV['OPEN_WEATHER_MAP_KEY']
        );
        $openWeatherMap->getData('Oz');
    }
}
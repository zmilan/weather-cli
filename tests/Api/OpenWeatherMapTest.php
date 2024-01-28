<?php


namespace Weather\Api;

use Mockery;
use PHPUnit\Framework\TestCase;
use Weather\Contract\HttpRequestContract;
use Weather\DTO\OpenWeatherMapConfiguration;
use Weather\DTO\ResponseData;
use Weather\Enum\Unit;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiRequestException;

class OpenWeatherMapTest extends TestCase
{
    private function getOpenWeatherMapConfig(): OpenWeatherMapConfiguration
    {
        return new OpenWeatherMapConfiguration(
            'https://testurl.com',
            'some-key',
            Unit::fromEnv(null)
        );
    }
    public function testGetWeatherSuccessfully(): void
    {
        $responseData = new ResponseData(
            'Sunny',
            '22',
            'Kranj',
            []
        );

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andReturn($responseData);

        $openWeatherMap = new OpenWeatherMap(
            $this->getOpenWeatherMapConfig(),
            $httpRequestMock
        );
        $weatherData = $openWeatherMap->getWeather('Kranj, SI');
        self::assertEquals('Kranj', $weatherData->name);
    }

    public function testGetWeatherWrongOpenWeatherMapConfig(): void
    {
        $this->expectException(WeatherApiRequestException::class);

        $openWeatherMapConfig = new OpenWeatherMapConfiguration(
            '',
            '',
            null
        );;

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andThrow(new WeatherApiRequestException);

        $openWeatherMap = new OpenWeatherMap(
            $openWeatherMapConfig,
            $httpRequestMock
        );
        $openWeatherMap->getWeather('Kranj, SI');
    }

    public function testGetWeatherEmptyQueryString(): void
    {
        $this->expectException(WeatherApiRequestException::class);

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andThrow(new WeatherApiRequestException);

        $openWeatherMap = new OpenWeatherMap(
            $this->getOpenWeatherMapConfig(),
            $httpRequestMock
        );

        $openWeatherMap->getWeather('');
    }

    public function testGetWeatherOzCity(): void
    {
        $this->expectException(WeatherApiDataException::class);

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andThrow(new WeatherApiDataException);

        $openWeatherMap = new OpenWeatherMap(
            $this->getOpenWeatherMapConfig(),
            $httpRequestMock
        );
        $openWeatherMap->getWeather('Oz');
    }
}
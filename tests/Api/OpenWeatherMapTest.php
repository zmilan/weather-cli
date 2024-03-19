<?php

namespace Tests\Api;

use Mockery;
use PHPUnit\Framework\TestCase;
use Weather\Api\OpenWeatherMap;
use Weather\Contract\HttpRequestContract;
use Weather\DTO\OpenWeatherMapConfiguration;
use Weather\DTO\RequestData;
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

    /**
     * @covers \Weather\Api\OpenWeatherMap::getWeather
     */
    public function testGetWeatherSuccessfully(): void
    {
        $responseData = new ResponseData(
            'Sunny',
            '22',
            Unit::fromEnv(null),
            'Kranj',
            []
        );

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andReturn($responseData);

        $openWeatherMapConfig = $this->getOpenWeatherMapConfig();
        $openWeatherMap = new OpenWeatherMap(
            $openWeatherMapConfig,
            $httpRequestMock
        );
        $requestData = new RequestData(
            'Kranj, SI',
            Unit::fromEnv(null),
        );
        $weatherData = $openWeatherMap->getWeather($requestData);
        self::assertEquals('Kranj', $weatherData->name);
    }

    /**
     * @covers \Weather\Api\OpenWeatherMap::getWeather
     */
    public function testGetWeatherWrongOpenWeatherMapConfig(): void
    {
        $this->expectException(WeatherApiRequestException::class);

        $openWeatherMapConfig = new OpenWeatherMapConfiguration(
            '',
            '',
            Unit::fromEnv(null)
        );;

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andThrow(new WeatherApiRequestException);

        $openWeatherMap = new OpenWeatherMap(
            $openWeatherMapConfig,
            $httpRequestMock
        );

        $requestData = new RequestData(
            'Kranj, SI',
            Unit::fromEnv(null),
        );
        $openWeatherMap->getWeather($requestData);
    }

    /**
     * @covers \Weather\Api\OpenWeatherMap::getWeather
     */
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

        $requestData = new RequestData(
            '',
            Unit::fromEnv(null),
        );

        $openWeatherMap->getWeather($requestData);
    }

    /**
     * @covers \Weather\Api\OpenWeatherMap::getWeather
     */
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

        $requestData = new RequestData(
            'Oz',
            Unit::fromEnv(null),
        );
        $openWeatherMap->getWeather($requestData);
    }
}
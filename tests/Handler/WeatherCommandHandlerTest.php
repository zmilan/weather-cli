<?php

namespace Weather\Handler;

use Weather\Api\OpenWeatherMap;
use Weather\Contract\HttpRequestContract;
use Weather\DTO\InputData;
use Weather\DTO\OpenWeatherMapConfiguration;
use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;
use Weather\Enum\Unit;
use Weather\Exception\InvalidInputException;
use PHPUnit\Framework\TestCase;
use Mockery;

class WeatherCommandHandlerTest extends TestCase
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
     * @covers \Weather\Handler\WeatherCommandHandler::execute
     */
    public function testExecute(): void
    {
        $apiConfig = $this->getOpenWeatherMapConfig();
        $requestData = new RequestData(
            'DummyCity, DummyCountry',
            $apiConfig->unit
        );
        $responseData = new ResponseData(
            'Sunny',
            '22',
            $apiConfig->unit,
            'DummyCity',
            []
        );

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andReturn($responseData);

        $api = Mockery::mock(OpenWeatherMap::class, [$apiConfig, $httpRequestMock]);
        $api->shouldReceive('getWeather')->once()->withArgs(function ($arg) use ($requestData) {
            return $arg->query === $requestData->query && $arg->unit === $requestData->unit;
        });
        
        $inputData = new InputData('DummyCity', 'DummyCountry');

        $weatherCommandHandler = new WeatherCommandHandler($api);
        $weatherCommandHandler->execute($inputData);
        
        $this->assertTrue(true);
    }

    /**
     * @covers \Weather\Handler\WeatherCommandHandler::execute
     */
    public function testExecuteWithEmptyCity(): void
    {
        $this->expectException(InvalidInputException::class);

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $api = Mockery::mock(OpenWeatherMap::class, [$this->getOpenWeatherMapConfig(), $httpRequestMock]);
        $inputData = new InputData('', 'DummyCountry');

        $weatherCommandHandler = new WeatherCommandHandler($api);
        $weatherCommandHandler->execute($inputData);
    }

    /**
     * @covers \Weather\Handler\WeatherCommandHandler::execute
     */
    public function testExecuteWithEmptyCountry(): void
    {
        $apiConfig = $this->getOpenWeatherMapConfig();
        $requestData = new RequestData(
            'DummyCity',
            $apiConfig->unit
        );
        $responseData = new ResponseData(
            'Sunny',
            '22',
            $apiConfig->unit,
            'DummyCity',
            []
        );

        $httpRequestMock = Mockery::mock(HttpRequestContract::class);
        $httpRequestMock->shouldReceive('getData')
            ->andReturn($responseData);

        $api = Mockery::mock(OpenWeatherMap::class, [$apiConfig, $httpRequestMock]);
        $api->shouldReceive('getWeather')->once()->withArgs(function ($arg) use ($requestData) {
            return $arg->query === $requestData->query && $arg->unit === $requestData->unit;
        });

        $inputData = new InputData('DummyCity', null);

        $weatherCommandHandler = new WeatherCommandHandler($api);
        $weatherCommandHandler->execute($inputData);

        $this->assertTrue(true);
    }
}
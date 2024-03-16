<?php

namespace Weather\Service;

use PHPUnit\Framework\TestCase;
use Weather\DTO\RequestData;
use Weather\Enum\Unit;
use Weather\Exception\WeatherApiProcessException;
use Weather\Exception\WeatherApiRequestException;

class SymfonyHttpRequestTest extends TestCase
{
    private SymfonyHttpRequest $request;

    private string $apiUrl;
    private string $apiKey;

    protected function setUp(): void
    {
        $this->request = new SymfonyHttpRequest();
        $this->apiUrl = $_ENV['OPEN_WEATHER_MAP_URL'];
        $this->apiKey = $_ENV['OPEN_WEATHER_MAP_KEY'];
    }

    /**
     * @covers \Weather\Service\SymfonyHttpRequest::getData
     */
    public function testGetDataSuccessfully(): void
    {
        $requestData = new RequestData('London, UK', Unit::Metric);
        $res = $this->request->getData($this->apiUrl, $this->apiKey, $requestData);

        $this->assertEquals('London', $res->name);
    }

    /**
     * @covers \Weather\Service\SymfonyHttpRequest::getData
     */
    public function testGetDataWithInvalidApiKey(): void
    {
        $this->expectException(WeatherApiProcessException::class);

        $requestData = new RequestData('London, UK', Unit::Metric);
        $this->request->getData($this->apiUrl, 'invalidKey', $requestData);
    }

    /**
     * @covers \Weather\Service\SymfonyHttpRequest::getData
     */
    public function testGetDataWithInvalidApiUrl(): void
    {
        $this->expectException(WeatherApiRequestException::class);

        $requestData = new RequestData('London, UK', Unit::Metric);
        $this->request->getData('invalid url', $this->apiKey, $requestData);
    }

    /**
     * @covers \Weather\Service\SymfonyHttpRequest::getData
     */
    public function testGetDataWithUnsuccessfulStatusCode(): void
    {
        $this->expectException(WeatherApiProcessException::class);

        $requestData = new RequestData('invalid request data', Unit::Metric);
        $this->request->getData($this->apiUrl, $this->apiKey, $requestData);
    }

    /**
     * @covers \Weather\Service\SymfonyHttpRequest::getData
     */
    public function testGetDataWhenServerErrorOccurs(): void
    {
        $this->expectException(WeatherApiProcessException::class);

        $requestData = new RequestData('London, UK', Unit::Metric);
        $this->request->getData('https://error.api.url', $this->apiKey, $requestData);
    }
}
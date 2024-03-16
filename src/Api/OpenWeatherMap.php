<?php
declare(strict_types=1);

namespace Weather\Api;

use Weather\Contract\HttpRequestContract;
use Weather\DTO\OpenWeatherMapConfiguration;
use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiProcessException;
use Weather\Exception\WeatherApiRequestException;

/**
 * Class OpenWeatherMap. Extreme simple implementation of [OpenWeatherMap](https://openweathermap.org/api)
 * weather service.
 *
 * @author [Milan Zivkovic](https://github.com/zmilan)
 * @package Weather\Api
 */
class OpenWeatherMap
{
    /**
     * @param OpenWeatherMapConfiguration $apiConfig
     * @param HttpRequestContract $httpRequest
     */
    public function __construct(
        public readonly OpenWeatherMapConfiguration $apiConfig,
        private readonly HttpRequestContract         $httpRequest
    ){}

    /**
     * Call API and return result in form of array.
     *
     * @param RequestData $request
     * @return ResponseData
     * @throws WeatherApiDataException
     * @throws WeatherApiProcessException
     * @throws WeatherApiRequestException
     */
    public function getWeather(RequestData $request): ResponseData
    {
        return $this->httpRequest->getData($this->apiConfig->apiUrl, $this->apiConfig->apiId, $request);
    }
}
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
readonly class OpenWeatherMap
{
    /**
     * @param OpenWeatherMapConfiguration $apiConfig
     * @param HttpRequestContract $httpRequest
     */
    public function __construct(
        public OpenWeatherMapConfiguration $apiConfig,
        private HttpRequestContract         $httpRequest
    ){}

    /**
     * Call API and return result in form of array.
     *
     * @param string $query
     *
     * @return ResponseData
     * @throws WeatherApiDataException
     * @throws WeatherApiProcessException
     * @throws WeatherApiRequestException
     */
    public function getWeather(string $query): ResponseData
    {
        $requestData = new RequestData(
            $this->apiConfig->apiUrl,
            $this->apiConfig->apiId,
            $query,
            $this->apiConfig->units
        );
        return $this->httpRequest->getData($requestData);
    }
}
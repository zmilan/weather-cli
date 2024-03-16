<?php

namespace Weather\Handler;

use Weather\Api\OpenWeatherMap;
use Weather\DTO\InputData;
use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;
use Weather\Exception\InvalidInputException;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiProcessException;
use Weather\Exception\WeatherApiRequestException;

readonly class WeatherCommandHandler
{
    /**
     * Class constructor.
     *
     * @param OpenWeatherMap $api The OpenWeatherMap instance.
     */
    public function __construct(private OpenWeatherMap $api)
    {
    }

    /**
     * Handles a query and returns the response data.
     *
     * @param InputData $inputData
     * @return ResponseData The response data.
     * @throws WeatherApiDataException
     * @throws WeatherApiProcessException
     * @throws WeatherApiRequestException
     * @throws InvalidInputException
     */
    public function execute(InputData $inputData): ResponseData
    {
        $query = $this->buildQuery($inputData);
        $requestData = new RequestData(
            $query,
            $this->api->apiConfig->unit
        );
        return $this->api->getWeather($requestData);
    }

    /**
     * Builds the query string based on the given input data.
     *
     * @param InputData $inputData The input data object.
     * @return string The built query string.
     * @throws InvalidInputException if the city parameter is empty.
     */
    private function buildQuery(InputData $inputData): string
    {
        if (empty($inputData->city)) {
            throw new InvalidInputException('City parameter is mandatory!');
        }
        $query = $inputData->city;
        if (!empty($inputData->country)) {
            $query = "{$query}, {$inputData->country}";
        }
        return $query;
    }
}
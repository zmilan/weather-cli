<?php

namespace Weather\Contract;

use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiProcessException;
use Weather\Exception\WeatherApiRequestException;

interface HttpRequestContract
{
    /**
     * @return ResponseData Structured data from OpenWeatherMap
     *
     * @throws WeatherApiDataException
     * @throws WeatherApiProcessException
     * @throws WeatherApiRequestException
     */
    public function getData(RequestData $requestData): ResponseData;
}
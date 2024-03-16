<?php

namespace Weather\Contract;

use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;

interface HttpRequestContract
{
    /**
     * Retrieves data from an API using the provided API URL, API key, and request data.
     *
     * @param string $apiUrl The URL of the API.
     * @param string $apiKey The API key used for authentication.
     * @param RequestData $requestData The request data object containing structured data from OpenWeatherMap for the API.
     *
     * @return ResponseData The response data object containing the data retrieved from the API.
     */
    public function getData(string $apiUrl, string $apiKey, RequestData $requestData): ResponseData;
}
<?php

namespace Weather\DTO;

class ResponseData
{
    /**
     * @param string $description The main description of the weather.
     * @param string $temperature The temperature of the city.
     * @param string $name The name of the place/city.
     * @param array $rawData The raw data result from OpenWeatherMap API call
     */
    public function __construct(
        public readonly string $description,
        public readonly string $temperature,
        public readonly string $name,
        public readonly array $rawData
    ) {}
}
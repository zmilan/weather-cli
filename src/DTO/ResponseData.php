<?php

namespace Weather\DTO;

use Weather\Enum\Unit;

class ResponseData
{
    /**
     * @param string $description The main description of the weather.
     * @param string $temperature The temperature of the city.
     * @param Unit $unit The unit of measurement for the API data.
     * @param string $name The name of the place/city.
     * @param array $rawData The raw data result from OpenWeatherMap API call
     */
    public function __construct(
        public readonly string $description,
        public readonly string $temperature,
        public readonly Unit $unit,
        public readonly string $name,
        public readonly array  $rawData
    ) {}
}
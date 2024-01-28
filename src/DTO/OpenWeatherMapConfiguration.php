<?php

namespace Weather\DTO;

use Weather\Enum\Unit;

readonly class OpenWeatherMapConfiguration
{
    /**
     * @param string $apiUrl The API URL to use for the request.
     * @param string $apiId
     * @param Unit|null $units The units to use for the request, or null if none specified.
     */
    public function __construct(
        public string $apiUrl,
        public string $apiId,
        public ?Unit $units,
    ){}
}
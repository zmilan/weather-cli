<?php

namespace Weather\DTO;

use Weather\Enum\Unit;

class OpenWeatherMapConfiguration
{
    /**
     * @param string $apiUrl The API URL to use for the request.
     * @param string $apiId
     * @param Unit $unit The unit to use for the request, default if none specified.
     */
    public function __construct(
        public readonly string $apiUrl,
        public readonly string $apiId,
        public readonly Unit $unit,
    ){}
}
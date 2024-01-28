<?php

namespace Weather\DTO;

use Weather\Enum\Unit;

class RequestData
{
    /**
     * @param string $apiUrl The base URL of the API.
     * @param string $query The search query for the API.
     * @param Unit|null $units The units of measurement for the API data.
     * @param string $appId The application ID for accessing the API.
     */
    public function __construct(
        public readonly string $apiUrl,
        public readonly string $appId,
        public readonly string $query,
        public readonly ?Unit $units
    ) {}
}
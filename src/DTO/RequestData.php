<?php

namespace Weather\DTO;

use Weather\Enum\Unit;

class RequestData
{
    /**
     * @param string $query The search query for the API.
     * @param Unit $unit The unit of measurement for the API data.
     */
    public function __construct(
        public readonly string $query,
        public readonly Unit  $unit
    ) {}
}
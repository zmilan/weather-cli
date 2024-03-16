<?php

namespace Weather\DTO;

class InputData
{
    public function __construct(
        public readonly ?string $city,
        public readonly ?string $country,
    ) {}
}
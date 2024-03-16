<?php

namespace Weather\Exception;

use Throwable;

class InvalidInputException extends \Exception
{
    protected $code = 4;

    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}
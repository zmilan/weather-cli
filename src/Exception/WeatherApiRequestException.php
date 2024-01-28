<?php
declare(strict_types=1);

namespace Weather\Exception;

use Throwable;

class WeatherApiRequestException extends WeatherApiException
{
    protected $code = 2;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     *
     * @param string $message  [optional] The Exception message to throw.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}
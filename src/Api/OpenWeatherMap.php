<?php
declare(strict_types=1);

namespace Weather\Api;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiRequestException;
use Weather\Exception\WeatherApiProcessException;

/**
 * Class OpenWeatherMap. Extreme simple implementation of [OpenWeatherMap](https://openweathermap.org/api)
 * weather service.
 *
 * @author [Milan Zivkovic](https://github.com/zmilan)
 * @package Weather\Api
 */
class OpenWeatherMap
{
    /**
     * API url
     * @var string|null
     */
    protected ?string $api_url;

    /**
     * Units of measurement. standard, metric and imperial units are available. If you do not use the units parameter,
     * standard units will be applied by default.
     * @var string|null
     */
    protected ?string $units;

    /**
     * Your unique API key (you can always find it on your account page under the
     * @var string|null
     */
    protected ?string $key;

    /**
     * OpenWeatherMap constructor.
     *
     * @param string $api_url
     * @param string $key
     * @param string $units
     */
    public function __construct(string $api_url, string $key, string $units = 'metric')
    {
        $this->api_url = $api_url;
        $this->key = $key;
        $this->units = $units;
    }


    /**
     * Call API and return result in form of array.
     *
     * @param string $query
     *
     * @return array
     * @throws \Weather\Exception\WeatherApiProcessException
     * @throws \Weather\Exception\WeatherApiRequestException
     * @throws \Weather\Exception\WeatherApiDataException
     */
    public function getData(string $query): ?array
    {
        // https://api.openweathermap.org/data/2.5/weather?q={city name},{state code},{country code}&units=metric&appid={API key}
        $client = HttpClient::create([
            'max_redirects' => 2,
        ]);

        try {
            $response = $client->request('GET', $this->api_url, [
                'query' => [
                    'q' => $query,
                    'units' => $this->units,
                    'appid' => $this->key
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if (200 !== $statusCode) {
                switch ($statusCode) {
                    case 401:
                        throw new WeatherApiRequestException($this->parseError($response,
                            'Authentication error.'));
                    case 404:
                        throw new WeatherApiDataException($this->parseError($response,
                            'Information not found.'));
                    default:
                        throw new WeatherApiRequestException($this->parseError($response,
                            'Unexpected error occurred.'));
                }
            }

            return $response->toArray();
        } catch (InvalidArgumentException $e) {
            throw new WeatherApiRequestException($e->getMessage(), $e);
        } catch (HttpExceptionInterface $e) {
            throw new WeatherApiRequestException($e->getMessage(), $e);
        } catch (ExceptionInterface $e) {
            throw new WeatherApiProcessException($e->getMessage(), $e);
        }
    }

    /**
     * Check if we could make sense from response content, otherwise return default message.
     *
     * @param \Symfony\Contracts\HttpClient\ResponseInterface $response
     * @param                                                 $default
     *
     * @return string
     */
    protected function parseError(ResponseInterface $response, $default): string
    {
        $content = $response->toArray(false);
        if (is_array($content) && isset($content['message'])) {
            return ucfirst($content['message']);
        }

        return ucfirst($default);
    }
}
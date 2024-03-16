<?php

namespace Weather\Service;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;
use Weather\Contract\HttpRequestContract;
use Weather\DTO\RequestData;
use Weather\DTO\ResponseData;
use Weather\Exception\WeatherApiDataException;
use Weather\Exception\WeatherApiProcessException;
use Weather\Exception\WeatherApiRequestException;

class SymfonyHttpRequest implements HttpRequestContract
{

    /**
     * @inheritDoc
     * @throws WeatherApiRequestException
     * @throws WeatherApiProcessException
     * @throws WeatherApiDataException
     */
    public function getData(string $apiUrl, string $apiKey, RequestData $requestData): ResponseData
    {
        // https://api.openweathermap.org/data/2.5/weather?q={city name},{state code},{country code}&units=metric&appid={API key}
        $client = HttpClient::create([
            'max_redirects' => 2,
        ]);

        try {
            $response = $client->request('GET',$apiUrl, [
                'query' => [
                    'q' => $requestData->query,
                    'units' => $requestData->unit?->value,
                    'appid' =>  $apiKey
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if (200 !== $statusCode) {
                throw match ($statusCode) {
                    401 => new WeatherApiRequestException($this->getErrorMessageFromResponse($response,
                        'Authentication error.')),
                    404 => new WeatherApiDataException($this->getErrorMessageFromResponse($response,
                        'Information not found.')),
                    default => new WeatherApiRequestException($this->getErrorMessageFromResponse($response,
                        'Unexpected error occurred.')),
                };
            }

            $data = $response->toArray();
            // check if received data has right structure
            if (!isset($data['weather'][0]['main'], $data['main']['temp'], $data['name'])) {
                throw new WeatherApiDataException('We are not able to process response.');
            }

            return new ResponseData(
                $data['weather'][0]['main'],
                $data['main']['temp'],
                $requestData->unit,
                $data['name'],
                $data
            );
        } catch (InvalidArgumentException $e) {
            throw new WeatherApiRequestException($e->getMessage(), $e);
        } catch (HttpExceptionInterface $e) {
            throw new WeatherApiRequestException($e->getMessage(), $e);
        } catch (Throwable $e) {
            throw new WeatherApiProcessException($e->getMessage(), $e);
        }
    }

    /**
     * Check if we could make sense from response content, otherwise return default message.
     *
     * @param ResponseInterface $response
     * @param string $default
     *
     * @return string
     */
    private function getErrorMessageFromResponse(ResponseInterface $response, string $default): string
    {
        $content = $response->toArray(false);

        if (isset($content['message'])) {
            return ucfirst($content['message']);
        }

        return ucfirst($default);
    }
}
<?php

namespace NipFinder\service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use NipFinder\domain\ResponseDto;
use NipFinder\enums\ApiData;
use NipFinder\enums\RequestType;
use NipFinder\exception\ResponseException;

class ApiClient
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @param $baseUrl
     * @param $apiKey
     */
    public function __construct($baseUrl, $apiKey = null)
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if($apiKey !== null) {
            $headers[ApiData::API_KEY_NAME] = $apiKey;
        }

        $this->baseUrl = $baseUrl;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 5.0,
            'headers' => $headers,
        ]);
    }

    /**
     * @param $endpoint
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getRawData($endpoint): ?ResponseDto
    {
        try {
            $response = $this->client->request(RequestType::GET, $endpoint);

            return new ResponseDto(
                json_decode($response->getBody()->getContents()),
                $response->getStatusCode()
            );
        } catch (RequestException $e) {
            error_log('API: ' . $e->getMessage());

            return new ResponseDto(json_decode($e->getResponse()->getBody()), $e->getResponse()->getStatusCode());
        }
    }

    /**
     * @param $endpoint
     * @return object
     * @throws GuzzleException
     * @throws ResponseException
     */
    public function getData($endpoint): object
    {
        $rawData = $this->getRawData($endpoint);

        if ($rawData->getStatusCode() !== 200) {
            throw new ResponseException(($rawData->getContent()->detail != null) ? $rawData->getContent()->detail : "Podano zły klucz", $rawData->getStatusCode());
        }

        return $rawData->getContent();
    }

    /**
     * @param $endpoint
     * @param $data
     * @return ResponseDto|null
     * @throws GuzzleException
     */
    public function postRawData($endpoint, $data): ?ResponseDto
    {
        try {

            $response = $this->client->post($endpoint,
                [
                    'headers' => ['Accept' => 'application/json'],
                    RequestOptions::JSON => $data
                ]
            );

            return new ResponseDto(
                json_decode($response->getBody()->getContents()),
                $response->getStatusCode()
            );
        } catch (RequestException $e) {
            error_log('API: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param $endpoint
     * @param $data
     * @return object
     * @throws GuzzleException
     * @throws ResponseException
     */
    public function postData($endpoint, $data = []): object
    {
        $rawData = $this->postRawData($endpoint, $data);

        if ($rawData->getStatusCode() !== 200) {
            throw new ResponseException(($rawData->getContent()->detail != null) ? $rawData->getContent()->detail : "Podano zły klucz", $rawData->getStatusCode());
        }

        return $rawData->getContent();
    }
}

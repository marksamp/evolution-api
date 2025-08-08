<?php

namespace EvolutionAPI\Http;

use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class HttpClient
{
    private Client $client;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'timeout' => $config->getTimeout(),
            'base_uri' => $config->getBaseUrl(),
            'headers' => $config->getHeaders()
        ]);
    }

    /**
     * @throws EvolutionAPIException
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            $options = [];
            if (!empty($params)) {
                $options[RequestOptions::QUERY] = $params;
            }

            $response = $this->client->get($endpoint, $options);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new EvolutionAPIException(
                'Erro na requisição GET: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws EvolutionAPIException
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $options = [];
            if (!empty($data)) {
                $options[RequestOptions::JSON] = $data;
            }

            $response = $this->client->post($endpoint, $options);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new EvolutionAPIException(
                'Erro na requisição POST: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws EvolutionAPIException
     */
    public function put(string $endpoint, array $data = []): array
    {
        try {
            $options = [];
            if (!empty($data)) {
                $options[RequestOptions::JSON] = $data;
            }

            $response = $this->client->put($endpoint, $options);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new EvolutionAPIException(
                'Erro na requisição PUT: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws EvolutionAPIException
     */
    public function delete(string $endpoint): array
    {
        try {
            $response = $this->client->delete($endpoint);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new EvolutionAPIException(
                'Erro na requisição DELETE: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws EvolutionAPIException
     */
    private function handleResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        if ($statusCode >= 400) {
            throw new EvolutionAPIException(
                "Erro HTTP {$statusCode}: {$body}",
                $statusCode
            );
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EvolutionAPIException(
                'Erro ao decodificar JSON: ' . json_last_error_msg(),
                500
            );
        }

        return $decoded ?: [];
    }
}
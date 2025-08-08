<?php

namespace EvolutionAPI\Config;

class Config
{
    private string $baseUrl;
    private string $apiKey;
    private string $instanceName;
    private int $timeout;
    private array $headers;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        string $instanceName,
        int $timeout = 30
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->instanceName = $instanceName;
        $this->timeout = $timeout;
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'apikey' => $this->apiKey
        ];
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getInstanceUrl(): string
    {
        return $this->baseUrl . '/' . $this->instanceName;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function addHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }
}
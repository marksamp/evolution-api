<?php

namespace EvolutionAPI;

use EvolutionAPI\Config\Config;
use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Services\InstanceService;
use EvolutionAPI\Services\MessageService;
use EvolutionAPI\Services\ContactService;
use EvolutionAPI\Services\GroupService;
use EvolutionAPI\Services\WebhookService;

class EvolutionAPIClient
{
    private Config $config;
    private HttpClient $httpClient;
    private InstanceService $instanceService;
    private MessageService $messageService;
    private ContactService $contactService;
    private GroupService $groupService;
    private WebhookService $webhookService;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        string $instanceName,
        int $timeout = 30
    ) {
        $this->config = new Config($baseUrl, $apiKey, $instanceName, $timeout);
        $this->httpClient = new HttpClient($this->config);

        $this->instanceService = new InstanceService($this->httpClient);
        $this->messageService = new MessageService($this->httpClient, $this->config);
        $this->contactService = new ContactService($this->httpClient, $this->config);
        $this->groupService = new GroupService($this->httpClient, $this->config);
        $this->webhookService = new WebhookService($this->httpClient, $this->config);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    public function instance(): InstanceService
    {
        return $this->instanceService;
    }

    public function message(): MessageService
    {
        return $this->messageService;
    }

    public function contact(): ContactService
    {
        return $this->contactService;
    }

    public function group(): GroupService
    {
        return $this->groupService;
    }

    public function webhook(): WebhookService
    {
        return $this->webhookService;
    }

    /**
     * Método de conveniência para criar e conectar instância
     */
    public function quickStart(array $settings = []): array
    {
        try {
            // Tenta conectar primeiro
            $connection = $this->instance()->getConnectionStatus($this->config->getInstanceName());

            if (isset($connection['instance']['state']) && $connection['instance']['state'] === 'open') {
                return $connection;
            }
        } catch (\Exception $e) {
            // Se não conseguir obter status, tenta criar
        }

        // Cria a instância
        $created = $this->instance()->create($this->config->getInstanceName(), $settings);

        // Conecta a instância
        $connected = $this->instance()->connect($this->config->getInstanceName());

        return [
            'created' => $created,
            'connected' => $connected
        ];
    }

    /**
     * Método de conveniência para verificar se está conectado
     */
    public function isConnected(): bool
    {
        try {
            $status = $this->instance()->getConnectionStatus($this->config->getInstanceName());
            return isset($status['instance']['state']) && $status['instance']['state'] === 'open';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Método de conveniência para enviar mensagem rápida
     */
    public function sendQuickMessage(string $number, string $message): array
    {
        return $this->message()->sendText($number, $message);
    }

    /**
     * Método de conveniência para verificar se número existe
     */
    public function checkNumber(string $number): bool
    {
        try {
            $result = $this->contact()->checkExists([$number]);
            return !empty($result) && isset($result[0]['exists']) && $result[0]['exists'];
        } catch (\Exception $e) {
            return false;
        }
    }
}
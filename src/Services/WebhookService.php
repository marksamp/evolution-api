<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class WebhookService
{
    private HttpClient $httpClient;
    private Config $config;

    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Configura webhook para a instância
     * @throws EvolutionAPIException
     */
    public function set(string $url, array $events = [], bool $enabled = true): array
    {
        $data = [
            'webhook' => [
                'url' => $url,
                'enabled' => $enabled,
                'events' => !empty($events) ? $events : [
                    'APPLICATION_STARTUP',
                    'QRCODE_UPDATED',
                    'MESSAGES_UPSERT',
                    'MESSAGES_UPDATE',
                    'MESSAGES_DELETE',
                    'SEND_MESSAGE',
                    'CONTACTS_SET',
                    'CONTACTS_UPSERT',
                    'CONTACTS_UPDATE',
                    'PRESENCE_UPDATE',
                    'CHATS_SET',
                    'CHATS_UPSERT',
                    'CHATS_UPDATE',
                    'CHATS_DELETE',
                    'GROUPS_UPSERT',
                    'GROUP_UPDATE',
                    'GROUP_PARTICIPANTS_UPDATE',
                    'CONNECTION_UPDATE'
                ]
            ]
        ];

        return $this->httpClient->post(
            "/webhook/set/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Obtém configurações do webhook
     * @throws EvolutionAPIException
     */
    public function get(): array
    {
        return $this->httpClient->get(
            "/webhook/find/{$this->config->getInstanceName()}"
        );
    }

    /**
     * Remove webhook da instância
     * @throws EvolutionAPIException
     */
    public function remove(): array
    {
        return $this->httpClient->delete(
            "/webhook/delete/{$this->config->getInstanceName()}"
        );
    }

    /**
     * Configura webhook global
     * @throws EvolutionAPIException
     */
    public function setGlobal(string $url, array $events = [], bool $enabled = true): array
    {
        $data = [
            'url' => $url,
            'enabled' => $enabled,
            'events' => !empty($events) ? $events : [
                'APPLICATION_STARTUP',
                'QRCODE_UPDATED',
                'MESSAGES_UPSERT',
                'MESSAGES_UPDATE',
                'MESSAGES_DELETE',
                'SEND_MESSAGE',
                'CONTACTS_SET',
                'CONTACTS_UPSERT',
                'CONTACTS_UPDATE',
                'PRESENCE_UPDATE',
                'CHATS_SET',
                'CHATS_UPSERT',
                'CHATS_UPDATE',
                'CHATS_DELETE',
                'GROUPS_UPSERT',
                'GROUP_UPDATE',
                'GROUP_PARTICIPANTS_UPDATE',
                'CONNECTION_UPDATE'
            ]
        ];

        return $this->httpClient->post('/webhook/globalwebhook', $data);
    }

    /**
     * Obtém webhook global
     * @throws EvolutionAPIException
     */
    public function getGlobal(): array
    {
        return $this->httpClient->get('/webhook/globalwebhook');
    }

    /**
     * Processa webhook recebido
     */
    public function processWebhook(string $payload): array
    {
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EvolutionAPIException(
                'Erro ao decodificar payload do webhook: ' . json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * Valida assinatura do webhook (se configurada)
     */
    public function validateSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class MessageService
{
    private HttpClient $httpClient;
    private Config $config;

    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Envia mensagem de texto
     * @throws EvolutionAPIException
     */
    public function sendText(string $number, string $text, array $options = []): array
    {
        $data = array_merge([
            'number' => $this->formatNumber($number),
            'text' => $text
        ], $options);

        return $this->httpClient->post(
            "/message/sendText/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia mídia (imagem, vídeo, áudio, documento)
     * @throws EvolutionAPIException
     */
    public function sendMedia(
        string $number,
        string $mediaUrl,
        string $mediaType = 'image',
        string $caption = '',
        string $fileName = ''
    ): array {
        $data = [
            'number' => $this->formatNumber($number),
            'mediatype' => $mediaType,
            'media' => $mediaUrl,
        ];

        if (!empty($caption)) {
            $data['mediaMessage']['caption'] = $caption;
        }

        if (!empty($fileName)) {
            $data['mediaMessage']['fileName'] = $fileName;
        }

        return $this->httpClient->post(
            "/message/sendMedia/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia mensagem de áudio
     * @throws EvolutionAPIException
     */
    public function sendAudio(string $number, string $audioUrl, bool $ptt = false): array
    {
        $data = [
            'number' => $this->formatNumber($number),
            'audioMessage' => [
                'audio' => $audioUrl,
                'ptt' => $ptt
            ]
        ];

        return $this->httpClient->post(
            "/message/sendWhatsAppAudio/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia localização
     * @throws EvolutionAPIException
     */
    public function sendLocation(
        string $number,
        float $latitude,
        float $longitude,
        string $name = '',
        string $address = ''
    ): array {
        $data = [
            'number' => $this->formatNumber($number),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'name' => $name,
            'address' => $address
        ];

        return $this->httpClient->post(
            "/message/sendLocation/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia contato
     * @throws EvolutionAPIException
     */
    public function sendContact(string $number, array $contacts): array
    {
        $data = [
            'number' => $this->formatNumber($number),
            'contactMessage' => $contacts
        ];

        return $this->httpClient->post(
            "/message/sendContact/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia lista interativa
     * @throws EvolutionAPIException
     */
    public function sendList(
        string $number,
        string $title,
        string $description,
        string $buttonText,
        array $sections
    ): array {
        $data = [
            'number' => $this->formatNumber($number),
            'listMessage' => [
                'title' => $title,
                'description' => $description,
                'buttonText' => $buttonText,
                'footerText' => '',
                'sections' => $sections
            ]
        ];

        return $this->httpClient->post(
            "/message/sendList/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia botões
     * @throws EvolutionAPIException
     */
    public function sendButtons(
        string $number,
        string $title,
        string $description,
        array $buttons,
        string $footer = ''
    ): array {
        $data = [
            'number' => $this->formatNumber($number),
            'buttonMessage' => [
                'title' => $title,
                'description' => $description,
                'footer' => $footer,
                'buttons' => $buttons
            ]
        ];

        return $this->httpClient->post(
            "/message/sendButtons/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Busca mensagens
     * @throws EvolutionAPIException
     */
    public function findMessages(array $filters = []): array
    {
        return $this->httpClient->post(
            "/chat/findMessages/{$this->config->getInstanceName()}",
            $filters
        );
    }

    /**
     * Marca mensagem como lida
     * @throws EvolutionAPIException
     */
    public function markAsRead(string $remoteJid, array $messageIds): array
    {
        $data = [
            'readMessages' => [
                'remoteJid' => $remoteJid,
                'fromMe' => false,
                'id' => $messageIds
            ]
        ];

        return $this->httpClient->put(
            "/chat/markMessageAsRead/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Deleta mensagem
     * @throws EvolutionAPIException
     */
    public function deleteMessage(string $messageId, string $remoteJid, bool $fromMe = true): array
    {
        $data = [
            'id' => $messageId,
            'remoteJid' => $remoteJid,
            'fromMe' => $fromMe
        ];

        return $this->httpClient->delete(
            "/message/delete/{$this->config->getInstanceName()}"
        );
    }

    /**
     * Formata número para padrão internacional
     */
    private function formatNumber(string $number): string
    {
        // Remove caracteres não numéricos
        $number = preg_replace('/[^0-9]/', '', $number);

        // Se não começar com código do país, adiciona 55 (Brasil)
        if (substr($number, 0, 2) !== '55' && strlen($number) === 11) {
            $number = '55' . $number;
        }

        return $number . '@s.whatsapp.net';
    }
}
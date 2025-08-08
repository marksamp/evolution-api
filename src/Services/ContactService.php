<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class ContactService
{
    private HttpClient $httpClient;
    private Config $config;

    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Busca todos os contatos
     * @throws EvolutionAPIException
     */
    public function fetchAll(): array
    {
//        return $this->httpClient->get(
//            "/chat/fetchContacts/{$this->config->getInstanceName()}"
//        );

        return $this->httpClient->get(
            "/chat/findContacts/{$this->config->getInstanceName()}"
        );
    }

    /**
     * Busca contato específico
     * @throws EvolutionAPIException
     */
    public function fetch(string $number): array
    {
        $data = [
            'where' => [
                'remoteJid' => $this->formatNumber($number)
            ]
        ];

        return $this->httpClient->post(
            "/chat/fetchContacts/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Verifica se número existe no WhatsApp
     * @throws EvolutionAPIException
     */
    public function checkExists(array $numbers): array
    {
        $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);

        $data = [
            'numbers' => $formattedNumbers
        ];

        return $this->httpClient->post(
            "/chat/whatsappNumbers/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Obtém foto do perfil
     * @throws EvolutionAPIException
     */
    public function getProfilePicture(string $number): array
    {
        $data = [
            'number' => $this->formatNumber($number)
        ];

        return $this->httpClient->post(
            "/chat/fetchProfilePictureUrl/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Obtém status do contato
     * @throws EvolutionAPIException
     */
    public function getStatus(string $number): array
    {
        $data = [
            'number' => $this->formatNumber($number)
        ];

        return $this->httpClient->post(
            "/chat/fetchProfile/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Bloqueia contato
     * @throws EvolutionAPIException
     */
    public function block(string $number): array
    {
        $data = [
            'number' => $this->formatNumber($number)
        ];

        return $this->httpClient->put(
            "/chat/blockUser/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Desbloqueia contato
     * @throws EvolutionAPIException
     */
    public function unblock(string $number): array
    {
        $data = [
            'number' => $this->formatNumber($number)
        ];

        return $this->httpClient->put(
            "/chat/unblockUser/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza foto do perfil próprio
     * @throws EvolutionAPIException
     */
    public function updateProfilePicture(string $imageUrl): array
    {
        $data = [
            'picture' => $imageUrl
        ];

        return $this->httpClient->put(
            "/chat/updateProfilePicture/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza nome do perfil próprio
     * @throws EvolutionAPIException
     */
    public function updateProfileName(string $name): array
    {
        $data = [
            'name' => $name
        ];

        return $this->httpClient->put(
            "/chat/updateProfileName/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza status do perfil próprio
     * @throws EvolutionAPIException
     */
    public function updateProfileStatus(string $status): array
    {
        $data = [
            'status' => $status
        ];

        return $this->httpClient->put(
            "/chat/updateProfileStatus/{$this->config->getInstanceName()}",
            $data
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
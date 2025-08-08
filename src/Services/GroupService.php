<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class GroupService
{
    private HttpClient $httpClient;
    private Config $config;

    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Lista todos os grupos
     * @throws EvolutionAPIException
     */
    public function fetchAll(): array
    {
        return $this->httpClient->get(
            "/group/fetchAllGroups/{$this->config->getInstanceName()}"
        );
    }

    /**
     * Cria um grupo
     * @throws EvolutionAPIException
     */
    public function create(string $subject, array $participants, string $description = ''): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'subject' => $subject,
            'description' => $description,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->post(
            "/group/create/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Obtém informações do grupo
     * @throws EvolutionAPIException
     */
    public function getInfo(string $groupId): array
    {
        $data = [
            'groupJid' => $groupId
        ];

        return $this->httpClient->post(
            "/group/findGroupInfos/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza foto do grupo
     * @throws EvolutionAPIException
     */
    public function updatePicture(string $groupId, string $imageUrl): array
    {
        $data = [
            'groupJid' => $groupId,
            'image' => $imageUrl
        ];

        return $this->httpClient->put(
            "/group/updateGroupPicture/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza assunto do grupo
     * @throws EvolutionAPIException
     */
    public function updateSubject(string $groupId, string $subject): array
    {
        $data = [
            'groupJid' => $groupId,
            'subject' => $subject
        ];

        return $this->httpClient->put(
            "/group/updateGroupSubject/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Atualiza descrição do grupo
     * @throws EvolutionAPIException
     */
    public function updateDescription(string $groupId, string $description): array
    {
        $data = [
            'groupJid' => $groupId,
            'description' => $description
        ];

        return $this->httpClient->put(
            "/group/updateGroupDescription/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Adiciona participantes ao grupo
     * @throws EvolutionAPIException
     */
    public function addParticipants(string $groupId, array $participants): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'groupJid' => $groupId,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->put(
            "/group/updateGroupParticipant/{$this->config->getInstanceName()}",
            array_merge($data, ['action' => 'add'])
        );
    }

    /**
     * Remove participantes do grupo
     * @throws EvolutionAPIException
     */
    public function removeParticipants(string $groupId, array $participants): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'groupJid' => $groupId,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->put(
            "/group/updateGroupParticipant/{$this->config->getInstanceName()}",
            array_merge($data, ['action' => 'remove'])
        );
    }

    /**
     * Promove participantes a admin
     * @throws EvolutionAPIException
     */
    public function promoteParticipants(string $groupId, array $participants): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'groupJid' => $groupId,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->put(
            "/group/updateGroupParticipant/{$this->config->getInstanceName()}",
            array_merge($data, ['action' => 'promote'])
        );
    }

    /**
     * Rebaixe participantes de admin
     * @throws EvolutionAPIException
     */
    public function demoteParticipants(string $groupId, array $participants): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'groupJid' => $groupId,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->put(
            "/group/updateGroupParticipant/{$this->config->getInstanceName()}",
            array_merge($data, ['action' => 'demote'])
        );
    }

    /**
     * Atualiza configurações do grupo
     * @throws EvolutionAPIException
     */
    public function updateSettings(string $groupId, array $settings): array
    {
        $data = array_merge([
            'groupJid' => $groupId
        ], $settings);

        return $this->httpClient->put(
            "/group/updateGroupSetting/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Sai do grupo
     * @throws EvolutionAPIException
     */
    public function leave(string $groupId): array
    {
        $data = [
            'groupJid' => $groupId
        ];

        return $this->httpClient->put(
            "/group/leaveGroup/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Obtém link de convite do grupo
     * @throws EvolutionAPIException
     */
    public function getInviteCode(string $groupId): array
    {
        $data = [
            'groupJid' => $groupId
        ];

        return $this->httpClient->post(
            "/group/inviteCode/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Revoga link de convite do grupo
     * @throws EvolutionAPIException
     */
    public function revokeInviteCode(string $groupId): array
    {
        $data = [
            'groupJid' => $groupId
        ];

        return $this->httpClient->put(
            "/group/revokeInviteCode/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia convite via link
     * @throws EvolutionAPIException
     */
    public function sendInvite(string $groupId, array $participants): array
    {
        $formattedParticipants = array_map([$this, 'formatNumber'], $participants);

        $data = [
            'groupJid' => $groupId,
            'participants' => $formattedParticipants
        ];

        return $this->httpClient->post(
            "/group/sendInvite/{$this->config->getInstanceName()}",
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
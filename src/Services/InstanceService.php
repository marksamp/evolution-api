<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class InstanceService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Cria uma nova instância
     * @throws EvolutionAPIException
     */
    public function create(string $instanceName, array $settings = []): array
    {
        $data = array_merge([
            'instanceName' => $instanceName,
            'qrcode' => true,
            'integration' => 'WHATSAPP-BAILEYS'
        ], $settings);

        return $this->httpClient->post('/instance/create', $data);
    }

    /**
     * Conecta a instância
     * @throws EvolutionAPIException
     */
    public function connect(string $instanceName): array
    {
        return $this->httpClient->get("/instance/connect/{$instanceName}");
    }

    /**
     * Obtém informações da instância
     * @throws EvolutionAPIException
     */
    public function getInfo(string $instanceName): array
    {
        return $this->httpClient->get("/instance/fetchInstances", [
            'instanceName' => $instanceName
        ]);
    }

    /**
     * Lista todas as instâncias
     * @throws EvolutionAPIException
     */
    public function listAll(): array
    {
        return $this->httpClient->get('/instance/fetchInstances');
    }

    /**
     * Obtém o status da conexão
     * @throws EvolutionAPIException
     */
    public function getConnectionStatus(string $instanceName): array
    {
        return $this->httpClient->get("/instance/connectionState/{$instanceName}");
    }

    /**
     * Desconecta a instância
     * @throws EvolutionAPIException
     */
    public function disconnect(string $instanceName): array
    {
        return $this->httpClient->delete("/instance/logout/{$instanceName}");
    }

    /**
     * Deleta a instância
     * @throws EvolutionAPIException
     */
    public function delete(string $instanceName): array
    {
        return $this->httpClient->delete("/instance/delete/{$instanceName}");
    }

    /**
     * Reinicia a instância
     * @throws EvolutionAPIException
     */
    public function restart(string $instanceName): array
    {
        return $this->httpClient->put("/instance/restart/{$instanceName}");
    }

    /**
     * Define configurações da instância
     * @throws EvolutionAPIException
     */
    public function setSettings(string $instanceName, array $settings): array
    {
        return $this->httpClient->put("/instance/settings/{$instanceName}", $settings);
    }
}
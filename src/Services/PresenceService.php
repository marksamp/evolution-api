<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;
use EvolutionAPI\Exceptions\EvolutionAPIException;

class PresenceService
{
    /** @var HttpClient */
    private $httpClient;
    /** @var Config */
    private $config;

    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Envia status de digitando
     * @param string $number
     * @param int $duration Duração em milissegundos (padrão: 3000ms = 3s)
     * @return array
     * @throws EvolutionAPIException
     */
    public function typing(string $number, int $duration = 3000): array
    {
        return $this->setPresence($number, 'composing', $duration);
    }

    /**
     * Envia status de gravando áudio
     * @param string $number
     * @param int $duration Duração em milissegundos (padrão: 3000ms = 3s)
     * @return array
     * @throws EvolutionAPIException
     */
    public function recording(string $number, int $duration = 3000): array
    {
        return $this->setPresence($number, 'recording', $duration);
    }

    /**
     * Envia status de pausado (para de digitar/gravar)
     * @param string $number
     * @return array
     * @throws EvolutionAPIException
     */
    public function paused(string $number): array
    {
        return $this->setPresence($number, 'paused', 0);
    }

    /**
     * Define status de presença
     * @param string $number
     * @param string $state Estado: composing (digitando), recording (gravando), paused (pausado)
     * @param int $delay Tempo em milissegundos
     * @return array
     * @throws EvolutionAPIException
     */
    public function setPresence(string $number, string $state = 'composing', int $delay = 3000): array
    {
        $data = [
            'number' => $this->formatNumber($number),
            'state' => $state
        ];

        if ($delay > 0) {
            $data['delay'] = $delay;
        }

        return $this->httpClient->post(
            "/chat/presence/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Marca presença como disponível
     * @return array
     * @throws EvolutionAPIException
     */
    public function available(): array
    {
        return $this->updatePresence('available');
    }

    /**
     * Marca presença como indisponível
     * @return array
     * @throws EvolutionAPIException
     */
    public function unavailable(): array
    {
        return $this->updatePresence('unavailable');
    }

    /**
     * Atualiza status de presença global
     * @param string $presence Estado: available (disponível) ou unavailable (indisponível)
     * @return array
     * @throws EvolutionAPIException
     */
    public function updatePresence(string $presence = 'available'): array
    {
        $data = [
            'presence' => $presence
        ];

        return $this->httpClient->post(
            "/chat/updatePresence/{$this->config->getInstanceName()}",
            $data
        );
    }

    /**
     * Envia "digitando" e aguarda antes de enviar mensagem
     * Simula comportamento humano
     * @param string $number
     * @param int $seconds Segundos de digitação (padrão: 2s)
     * @return void
     */
    public function simulateTyping(string $number, int $seconds = 2): void
    {
        try {
            $this->typing($number, $seconds * 1000);
            sleep($seconds);
        } catch (\Exception $e) {
            // Ignora erros de presença para não interromper o fluxo
        }
    }

    /**
     * Envia "gravando áudio" e aguarda antes de enviar áudio
     * Simula comportamento humano
     * @param string $number
     * @param int $seconds Segundos de gravação (padrão: 3s)
     * @return void
     */
    public function simulateRecording(string $number, int $seconds = 3): void
    {
        try {
            $this->recording($number, $seconds * 1000);
            sleep($seconds);
        } catch (\Exception $e) {
            // Ignora erros de presença para não interromper o fluxo
        }
    }

    /**
     * Formata número para padrão internacional
     * @param string $number
     * @return string
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
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Services\WebhookService;
use EvolutionAPI\Exceptions\EvolutionAPIException;

/**
 * Exemplo de handler para processar webhooks da EvolutionAPI
 * Este arquivo deve ser colocado em um endpoint público acessível pela API
 */

// Configuração da API
$baseUrl = 'https://sua-evolution-api.com';
$apiKey = 'sua-api-key';
$instanceName = 'minha-instancia';

// Opcional: chave secreta para validar webhooks
$webhookSecret = 'sua-chave-secreta'; // Deixe vazio se não usar

try {
    // Obter payload do webhook
    $payload = file_get_contents('php://input');

    if (empty($payload)) {
        http_response_code(400);
        echo json_encode(['error' => 'Payload vazio']);
        exit;
    }

    // Criar cliente
    $client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);
    $webhookService = $client->webhook();

    // Validar assinatura se configurada
    if (!empty($webhookSecret)) {
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

        if (!$webhookService->validateSignature($payload, $signature, $webhookSecret)) {
            http_response_code(401);
            echo json_encode(['error' => 'Assinatura inválida']);
            exit;
        }
    }

    // Processar webhook
    $data = $webhookService->processWebhook($payload);

    // Log do evento recebido
    error_log("Webhook recebido: " . json_encode($data));

    // Processar diferentes tipos de eventos
    if (isset($data['event'])) {
        switch ($data['event']) {
            case 'MESSAGES_UPSERT':
                handleNewMessage($client, $data);
                break;

            case 'QRCODE_UPDATED':
                handleQRCodeUpdate($data);
                break;

            case 'CONNECTION_UPDATE':
                handleConnectionUpdate($data);
                break;

            case 'GROUP_PARTICIPANTS_UPDATE':
                handleGroupUpdate($client, $data);
                break;

            case 'CONTACTS_UPSERT':
                handleContactUpdate($data);
                break;

            default:
                error_log("Evento não tratado: " . $data['event']);
        }
    }

    // Responder com sucesso
    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (EvolutionAPIException $e) {
    error_log("Erro EvolutionAPI: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno']);
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno']);
}

/**
 * Processa novas mensagens recebidas
 */
function handleNewMessage(EvolutionAPIClient $client, array $data): void
{
    if (!isset($data['data']['messages'])) {
        return;
    }

    foreach ($data['data']['messages'] as $message) {
        // Ignorar mensagens próprias
        if ($message['key']['fromMe'] ?? false) {
            continue;
        }

        $remoteJid = $message['key']['remoteJid'];
        $messageText = $message['message']['conversation'] ??
            $message['message']['extendedTextMessage']['text'] ?? '';

        error_log("Nova mensagem de {$remoteJid}: {$messageText}");

        // Exemplo de resposta automática
        if (strtolower($messageText) === 'oi' || strtolower($messageText) === 'olá') {
            try {
                $number = str_replace('@s.whatsapp.net', '', $remoteJid);
                $client->message()->sendText(
                    $number,
                    'Olá! Obrigado por entrar em contato. Como posso ajudá-lo?'
                );
            } catch (Exception $e) {
                error_log("Erro ao enviar resposta automática: " . $e->getMessage());
            }
        }

        // Marcar mensagem como lida
        try {
            $client->message()->markAsRead($remoteJid, [$message['key']['id']]);
        } catch (Exception $e) {
            error_log("Erro ao marcar como lida: " . $e->getMessage());
        }
    }
}

/**
 * Processa atualizações do QR Code
 */
function handleQRCodeUpdate(array $data): void
{
    if (isset($data['data']['qrcode'])) {
        error_log("QR Code atualizado");
        // Aqui você pode salvar o QR code ou notificar sobre a atualização
        // file_put_contents('/tmp/qrcode.png', base64_decode($data['data']['qrcode']));
    }
}

/**
 * Processa mudanças na conexão
 */
function handleConnectionUpdate(array $data): void
{
    $state = $data['data']['state'] ?? 'unknown';
    error_log("Status da conexão: {$state}");

    switch ($state) {
        case 'open':
            error_log("WhatsApp conectado com sucesso!");
            break;

        case 'close':
            error_log("WhatsApp desconectado!");
            break;

        case 'connecting':
            error_log("Conectando ao WhatsApp...");
            break;
    }
}

/**
 * Processa atualizações de grupos
 */
function handleGroupUpdate(EvolutionAPIClient $client, array $data): void
{
    $participants = $data['data']['participants'] ?? [];
    $action = $data['data']['action'] ?? '';
    $groupId = $data['data']['id'] ?? '';

    error_log("Atualização no grupo {$groupId}: {$action}");

    foreach ($participants as $participant) {
        error_log("Participante: {$participant}");
    }

    // Exemplo: enviar mensagem de boas-vindas
    if ($action === 'add') {
        try {
            $client->message()->sendText(
                str_replace('@g.us', '', $groupId),
                'Bem-vindos ao grupo! 👋'
            );
        } catch (Exception $e) {
            error_log("Erro ao enviar boas-vindas: " . $e->getMessage());
        }
    }
}

/**
 * Processa atualizações de contatos
 */
function handleContactUpdate(array $data): void
{
    if (isset($data['data']['contacts'])) {
        $contactCount = count($data['data']['contacts']);
        error_log("Atualização de contatos: {$contactCount} contatos");
    }
}
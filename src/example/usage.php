<?php

require_once __DIR__ . '/../vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

// Configuração da API
$baseUrl = 'https://sua-evolution-api.com';
$apiKey = 'sua-api-key';
$instanceName = 'minha-instancia';

try {
    // Criar cliente
    $client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);

    echo "=== Exemplo de uso da EvolutionAPI ===\n\n";

    // 1. Criar e conectar instância (método de conveniência)
    echo "1. Criando/conectando instância...\n";
    $quickStart = $client->quickStart([
        'qrcode' => true,
        'integration' => 'WHATSAPP-BAILEYS'
    ]);
    echo "Status: " . json_encode($quickStart) . "\n\n";

    // 2. Verificar status da conexão
    echo "2. Verificando conexão...\n";
    $isConnected = $client->isConnected();
    echo "Conectado: " . ($isConnected ? 'Sim' : 'Não') . "\n\n";

    if (!$isConnected) {
        echo "Instância não conectada. Verifique o QR Code e tente novamente.\n";
        exit;
    }

    // 3. Verificar se número existe
    echo "3. Verificando se número existe...\n";
    $number = '5511999999999'; // Substitua pelo número desejado
    $numberExists = $client->checkNumber($number);
    echo "Número {$number} existe: " . ($numberExists ? 'Sim' : 'Não') . "\n\n";

    if (!$numberExists) {
        echo "Número não existe no WhatsApp. Encerrando exemplo.\n";
        exit;
    }

    // 4. Enviar mensagem de texto
    echo "4. Enviando mensagem de texto...\n";
    $textMessage = $client->sendQuickMessage($number, 'Olá! Esta é uma mensagem de teste da EvolutionAPI.');
    echo "Mensagem enviada: " . json_encode($textMessage) . "\n\n";

    // 5. Enviar mídia
    echo "5. Enviando imagem...\n";
    $mediaMessage = $client->message()->sendMedia(
        $number,
        'https://via.placeholder.com/300x200.png',
        'image',
        'Esta é uma imagem de teste!'
    );
    echo "Mídia enviada: " . json_encode($mediaMessage) . "\n\n";

    // 6. Enviar localização
    echo "6. Enviando localização...\n";
    $locationMessage = $client->message()->sendLocation(
        $number,
        -3.7319,  // Latitude de Fortaleza
        -38.5267, // Longitude de Fortaleza
        'Fortaleza',
        'Fortaleza, Ceará, Brasil'
    );
    echo "Localização enviada: " . json_encode($locationMessage) . "\n\n";

    // 7. Enviar botões
    echo "7. Enviando mensagem com botões...\n";
    $buttons = [
        [
            'buttonId' => 'btn1',
            'buttonText' => ['displayText' => 'Opção 1'],
            'type' => 1
        ],
        [
            'buttonId' => 'btn2',
            'buttonText' => ['displayText' => 'Opção 2'],
            'type' => 1
        ]
    ];

    $buttonMessage = $client->message()->sendButtons(
        $number,
        'Escolha uma opção',
        'Esta é uma mensagem com botões interativos.',
        $buttons,
        'Rodapé da mensagem'
    );
    echo "Botões enviados: " . json_encode($buttonMessage) . "\n\n";

    // 8. Buscar contatos
    echo "8. Buscando contatos...\n";
    $contacts = $client->contact()->fetchAll();
    echo "Total de contatos: " . count($contacts) . "\n\n";

    // 9. Buscar grupos
    echo "9. Buscando grupos...\n";
    $groups = $client->group()->fetchAll();
    echo "Total de grupos: " . count($groups) . "\n\n";

    // 10. Configurar webhook
    echo "10. Configurando webhook...\n";
    $webhook = $client->webhook()->set(
        'https://seu-servidor.com/webhook',
        ['MESSAGES_UPSERT', 'SEND_MESSAGE']
    );
    echo "Webhook configurado: " . json_encode($webhook) . "\n\n";

    // 11. Buscar mensagens
    echo "11. Buscando mensagens...\n";
    $messages = $client->message()->findMessages([
        'where' => [
            'remoteJid' => $number . '@s.whatsapp.net'
        ],
        'limit' => 10
    ]);
    echo "Mensagens encontradas: " . count($messages) . "\n\n";

    // 12. Obter informações da instância
    echo "12. Obtendo informações da instância...\n";
    $instanceInfo = $client->instance()->getInfo($instanceName);
    echo "Info da instância: " . json_encode($instanceInfo) . "\n\n";

    echo "=== Exemplo concluído com sucesso! ===\n";

} catch (EvolutionAPIException $e) {
    echo "Erro da EvolutionAPI: " . $e->getMessage() . "\n";
    echo "Contexto: " . json_encode($e->getContext()) . "\n";
    echo "Código: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "\n";
}
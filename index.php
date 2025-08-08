<?php

require_once __DIR__ . '/vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

// Configuração da API
$baseUrl = 'https://apievolution.euregistro.com.br/';
$apiKey = '21F7488FCB70-476A-9D98-92C03325EBFD';
$instanceName = 'EuRegistro';

try {
    // Criar cliente
    $client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);

    echo "=== Exemplo de uso da EvolutionAPI ===<br><br>";

    // 1. Criar e conectar instância (método de conveniência)
    echo "1. Criando/conectando instância...<br>";
    $quickStart = $client->quickStart([
        'qrcode' => true,
        'integration' => 'WHATSAPP-BAILEYS'
    ]);
    echo "Status: " . json_encode($quickStart) . "<br><br>";

    // 2. Verificar status da conexão
    echo "2. Verificando conexão...<br>";
    $isConnected = $client->isConnected();
    echo "Conectado: " . ($isConnected ? 'Sim' : 'Não') . "<br><br>";

    if (!$isConnected) {
        echo "Instância não conectada. Verifique o QR Code e tente novamente.<br>";
        exit;
    }

    // 3. Verificar se número existe
    echo "3. Verificando se número existe...<br>";
    //$number = '5511999999999'; // Substitua pelo número desejado
    $number = '5585987435148'; // Substitua pelo número desejado
    $numberExists = $client->checkNumber($number);
    echo "Número {$number} existe: " . ($numberExists ? 'Sim' : 'Não') . "<br><br>";

    if (!$numberExists) {
        echo "Número não existe no WhatsApp. Encerrando exemplo.<br>";
        exit;
    }

    // 4. Enviar mensagem de texto
    echo "4. Enviando mensagem de texto...<br>";
    $textMessage = $client->sendQuickMessage($number, 'Olá! Esta é uma mensagem de teste da EvolutionAPI.');
    echo "Mensagem enviada: " . json_encode($textMessage) . "<br><br>";

    // 5. Enviar mídia
    echo "5. Enviando imagem...<br>";
    $mediaMessage = $client->message()->sendMedia(
        $number,
        'https://www.euregistrogrpi.com.br/grpiv2/images/grpi_logo2.png',
        'image',
        'Esta é uma imagem de teste!'
    );
    echo "Mídia enviada: " . json_encode($mediaMessage) . "<br><br>";

    // 6. Enviar localização
    echo "6. Enviando localização...<br>";
    $locationMessage = $client->message()->sendLocation(
        $number,
        -3.7319,  // Latitude de Fortaleza
        -38.5267, // Longitude de Fortaleza
        'Fortaleza',
        'Fortaleza, Ceará, Brasil'
    );
    echo "Localização enviada: " . json_encode($locationMessage) . "<br><br>";

    // 7. Enviar botões
//    echo "7. Enviando mensagem com botões...<br>";
//    $buttons = [
//        [
//            'buttonId' => 'btn1',
//            'buttonText' => ['displayText' => 'Opção 1'],
//            'type' => 1
//        ],
//        [
//            'buttonId' => 'btn2',
//            'buttonText' => ['displayText' => 'Opção 2'],
//            'type' => 1
//        ]
//    ];
//
//    $buttonMessage = $client->message()->sendButtons(
//        $number,
//        'Escolha uma opção',
//        'Esta é uma mensagem com botões interativos.',
//        $buttons,
//        'Rodapé da mensagem'
//    );
//    echo "Botões enviados: " . json_encode($buttonMessage) . "<br><br>";

    // 8. Buscar contatos
//    echo "8. Buscando contatos...<br>";
//    $contacts = $client->contact()->fetchAll();
//    echo "Total de contatos: " . count($contacts) . "<br><br>";
//
//    // 9. Buscar grupos
//    echo "9. Buscando grupos...<br>";
//    $groups = $client->group()->fetchAll();
//    echo "Total de grupos: " . count($groups) . "<br><br>";

    // 10. Configurar webhook
//    echo "10. Configurando webhook...<br>";
//    $webhook = $client->webhook()->set(
//        'https://seu-servidor.com/webhook',
//        ['MESSAGES_UPSERT', 'SEND_MESSAGE']
//    );
//    echo "Webhook configurado: " . json_encode($webhook) . "<br><br>";

    // 11. Buscar mensagens
    echo "11. Buscando mensagens...<br>";
    $messages = $client->message()->findMessages([
        'where' => [
            'remoteJid' => $number . '@s.whatsapp.net'
        ],
        'limit' => 10
    ]);
    echo "Mensagens encontradas: " . count($messages) . "<br><br>";

    // 12. Obter informações da instância
    echo "12. Obtendo informações da instância...<br>";
    $instanceInfo = $client->instance()->getInfo($instanceName);
    echo "Info da instância: " . json_encode($instanceInfo) . "<br><br>";

    echo "=== Exemplo concluído com sucesso! ===<br>";

} catch (EvolutionAPIException $e) {
    echo "Erro da EvolutionAPI: " . $e->getMessage() . "<br>";
    echo "Contexto: " . json_encode($e->getContext()) . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "<br>";
}
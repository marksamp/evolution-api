# EvolutionAPI PHP Client

Uma biblioteca PHP completa para integração com a EvolutionAPI, permitindo o controle total de instâncias do WhatsApp Business através de uma API REST.

## Características

- ✅ **PHP 7.4+** compatível
- ✅ **PSR-4** autoloading
- ✅ **Composer** para gerenciamento de dependências
- ✅ **Guzzle HTTP** para requisições robustas
- ✅ **Tratamento de exceções** personalizado
- ✅ **Arquitetura modular** com separação de responsabilidades
- ✅ **Métodos de conveniência** para tarefas comuns
- ✅ **Suporte completo** a webhooks
- ✅ **Documentação** e exemplos práticos

## Instalação

```bash
composer require evolution-api/php-client
```

## Configuração Básica

```php
<?php

require_once 'vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;

$client = new EvolutionAPIClient(
    'https://sua-evolution-api.com',  // URL base da API
    'sua-api-key',                    // Sua chave da API
    'minha-instancia'                 // Nome da instância
);
```

## Funcionalidades Principais

### 1. Gerenciamento de Instâncias

```php
// Criar uma nova instância
$instance = $client->instance()->create('minha-instancia', [
    'qrcode' => true,
    'integration' => 'WHATSAPP-BAILEYS'
]);

// Conectar instância
$connection = $client->instance()->connect('minha-instancia');

// Verificar status da conexão
$status = $client->instance()->getConnectionStatus('minha-instancia');

// Método de conveniência - criar e conectar automaticamente
$quickStart = $client->quickStart();
```

### 2. Envio de Mensagens

```php
// Mensagem de texto simples
$client->sendQuickMessage('5511999999999', 'Olá! Como você está?');

// Mensagem de texto com opções
$client->message()->sendText('5511999999999', 'Mensagem completa', [
    'delay' => 1000,
    'linkPreview' => false
]);

// Enviar mídia
$client->message()->sendMedia(
    '5511999999999',
    'https://exemplo.com/imagem.jpg',
    'image',
    'Legenda da imagem'
);

// Enviar áudio
$client->message()->sendAudio(
    '5511999999999',
    'https://exemplo.com/audio.mp3',
    true // PTT (Push to Talk)
);

// Enviar localização
$client->message()->sendLocation(
    '5511999999999',
    -3.7319,  // Latitude
    -38.5267, // Longitude
    'Fortaleza',
    'Fortaleza, Ceará, Brasil'
);

// Enviar contato
$client->message()->sendContact('5511999999999', [
    [
        'fullName' => 'João Silva',
        'waid' => '5511888888888',
        'phoneNumber' => '+55 11 88888-8888'
    ]
]);

// Enviar botões interativos
$buttons = [
    [
        'buttonId' => 'btn1',
        'buttonText' => ['displayText' => 'Sim'],
        'type' => 1
    ],
    [
        'buttonId' => 'btn2',
        'buttonText' => ['displayText' => 'Não'],
        'type' => 1
    ]
];

$client->message()->sendButtons(
    '5511999999999',
    'Confirma o pedido?',
    'Clique em uma opção abaixo',
    $buttons
);

// Enviar lista interativa
$sections = [
    [
        'title' => 'Produtos',
        'rows' => [
            [
                'rowId' => 'prod1',
                'title' => 'Produto 1',
                'description' => 'Descrição do produto 1'
            ],
            [
                'rowId' => 'prod2',
                'title' => 'Produto 2',
                'description' => 'Descrição do produto 2'
            ]
        ]
    ]
];

$client->message()->sendList(
    '5511999999999',
    'Catálogo de Produtos',
    'Escolha um produto',
    'Ver Produtos',
    $sections
);
```

### 3. Gerenciamento de Contatos

```php
// Listar todos os contatos
$contacts = $client->contact()->fetchAll();

// Buscar contato específico
$contact = $client->contact()->fetch('5511999999999');

// Verificar se números existem no WhatsApp
$numbers = ['5511999999999', '5511888888888'];
$exists = $client->contact()->checkExists($numbers);

// Método de conveniência
$exists = $client->checkNumber('5511999999999');

// Obter foto do perfil
$profilePic = $client->contact()->getProfilePicture('5511999999999');

// Bloquear/desbloquear contato
$client->contact()->block('5511999999999');
$client->contact()->unblock('5511999999999');

// Atualizar seu próprio perfil
$client->contact()->updateProfileName('Meu Nome');
$client->contact()->updateProfileStatus('Disponível');
$client->contact()->updateProfilePicture('https://exemplo.com/foto.jpg');
```

### 4. Gerenciamento de Grupos

```php
// Listar todos os grupos
$groups = $client->group()->fetchAll();

// Criar grupo
$participants = ['5511999999999', '5511888888888'];
$group = $client->group()->create(
    'Meu Grupo',
    $participants,
    'Descrição do grupo'
);

// Obter informações do grupo
$groupInfo = $client->group()->getInfo('120363012345678901@g.us');

// Atualizar grupo
$client->group()->updateSubject('120363012345678901@g.us', 'Novo Nome');
$client->group()->updateDescription('120363012345678901@g.us', 'Nova descrição');
$client->group()->updatePicture('120363012345678901@g.us', 'https://exemplo.com/foto.jpg');

// Gerenciar participantes
$client->group()->addParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->removeParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->promoteParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->demoteParticipants('120363012345678901@g.us', ['5511777777777']);

// Link de convite
$inviteCode = $client->group()->getInviteCode('120363012345678901@g.us');
$client->group()->revokeInviteCode('120363012345678901@g.us');

// Sair do grupo
$client->group()->leave('120363012345678901@g.us');
```

### 5. Webhooks

```php
// Configurar webhook para a instância
$client->webhook()->set(
    'https://seu-servidor.com/webhook',
    ['MESSAGES_UPSERT', 'SEND_MESSAGE', 'CONNECTION_UPDATE']
);

// Webhook global
$client->webhook()->setGlobal(
    'https://seu-servidor.com/webhook-global',
    ['MESSAGES_UPSERT']
);

// Obter configuração atual
$webhookConfig = $client->webhook()->get();

// Remover webhook
$client->webhook()->remove();
```

### 6. Busca de Mensagens

```php
// Buscar mensagens de um contato
$messages = $client->message()->findMessages([
    'where' => [
        'remoteJid' => '5511999999999@s.whatsapp.net'
    ],
    'limit' => 50
]);

// Marcar mensagens como lidas
$client->message()->markAsRead(
    '5511999999999@s.whatsapp.net',
    ['messageId1', 'messageId2']
);
```

## Tratamento de Exceções

```php
use EvolutionAPI\Exceptions\EvolutionAPIException;

try {
    $result = $client->sendQuickMessage('5511999999999', 'Teste');
} catch (EvolutionAPIException $e) {
    echo "Erro da API: " . $e->getMessage();
    echo "Código: " . $e->getCode();
    echo "Contexto: " . json_encode($e->getContext());
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage();
}
```

## Processamento de Webhooks

Crie um endpoint para receber webhooks:

```php
// webhook_handler.php
require_once 'vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;

$client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);
$payload = file_get_contents('php://input');
$data = $client->webhook()->processWebhook($payload);

// Processar diferentes tipos de eventos
switch ($data['event']) {
    case 'MESSAGES_UPSERT':
        // Nova mensagem recebida
        handleNewMessage($client, $data);
        break;
        
    case 'CONNECTION_UPDATE':
        // Status da conexão mudou
        handleConnectionUpdate($data);
        break;
        
    case 'QRCODE_UPDATED':
        // QR Code atualizado
        handleQRCodeUpdate($data);
        break;
}

function handleNewMessage($client, $data) {
    foreach ($data['data']['messages'] as $message) {
        if ($message['key']['fromMe']) continue;
        
        $number = str_replace('@s.whatsapp.net', '', $message['key']['remoteJid']);
        $text = $message['message']['conversation'] ?? '';
        
        // Resposta automática
        if (strtolower($text) === 'oi') {
            $client->sendQuickMessage($number, 'Olá! Como posso ajudar?');
        }
    }
}
```

## Métodos de Conveniência

A biblioteca oferece métodos simplificados para tarefas comuns:

```php
// Verificar se está conectado
$isConnected = $client->isConnected();

// Criar e conectar instância automaticamente
$client->quickStart();

// Enviar mensagem rápida
$client->sendQuickMessage('5511999999999', 'Mensagem');

// Verificar se número existe
$exists = $client->checkNumber('5511999999999');
```

## Estrutura do Projeto

```
src/
├── Config/
│   └── Config.php              # Configurações da API
├── Http/
│   └── HttpClient.php          # Cliente HTTP com Guzzle
├── Services/
│   ├── InstanceService.php     # Gerenciamento de instâncias
│   ├── MessageService.php      # Envio e gerenciamento de mensagens
│   ├── ContactService.php      # Gerenciamento de contatos
│   ├── GroupService.php        # Gerenciamento de grupos
│   └── WebhookService.php      # Configuração de webhooks
├── Exceptions/
│   └── EvolutionAPIException.php # Exceções personalizadas
└── EvolutionAPIClient.php      # Cliente principal
```

## Requisitos

- PHP 7.4 ou superior
- ext-json
- guzzlehttp/guzzle ^7.0

## Configuração de Timeout

```php
// Timeout personalizado (30 segundos por padrão)
$client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName, 60);

// Ou após a instanciação
$client->getConfig()->setTimeout(60);
```

## Headers Personalizados

```php
// Adicionar headers customizados
$client->getConfig()->addHeader('X-Custom-Header', 'valor');
```

## Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## Suporte

Para questões e suporte:

- Abra uma [issue](https://github.com/seu-usuario/evolution-api-php/issues)
- Consulte a [documentação oficial da EvolutionAPI](https://doc.evolution-api.com/)

## Changelog

### v1.0.0
- Versão inicial
- Suporte completo à EvolutionAPI
- Gerenciamento de instâncias, mensagens, contatos e grupos
- Sistema de webhooks
- Tratamento de exceções
- Métodos de conveniência
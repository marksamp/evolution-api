# 🚀 EvolutionAPI PHP Client

Uma biblioteca PHP completa e profissional para integração com a **EvolutionAPI**, permitindo o controle total de instâncias do WhatsApp Business através de uma API REST, com **proteção anti-bloqueio integrada**.

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Evolution API](https://img.shields.io/badge/Evolution%20API-Compatible-success.svg)](https://evolution-api.com)

---

## 📋 Índice

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Guia Rápido](#-guia-rápido)
- [Funcionalidades](#-funcionalidades)
- [SafeSender - Proteção Anti-Bloqueio](#️-safesender---proteção-anti-bloqueio)
- [Melhores Práticas](#-melhores-práticas)
- [Exemplos](#-exemplos)
- [FAQ](#-faq)
- [Tratamento de Exceções](#-tratamento-de-exceções)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

---

## ✨ Características

### Core Features
- ✅ **PHP 7.4+** compatível
- ✅ **PSR-4** autoloading via Composer
- ✅ **Guzzle HTTP** para requisições robustas
- ✅ **Arquitetura modular** com separação de responsabilidades
- ✅ **Tratamento completo** de exceções personalizadas
- ✅ **Documentação** completa e exemplos práticos

### Funcionalidades Principais
- 📱 Gerenciamento de instâncias (criar, conectar, status)
- 💬 Envio de mensagens (texto, mídia, áudio, localização, contatos)
- 🎯 Mensagens interativas (botões, listas)
- 👥 Gestão de contatos e grupos
- 🔔 Webhooks configuráveis
- ⚡ Presença ("digitando...", "gravando áudio...")
- 📊 QR Code automático para conexão

### 🛡️ Proteção Anti-Bloqueio (SafeSender)
- 🚦 Rate limiting automático inteligente
- 🤖 Humanização de comportamento
- ⏱️ Delays aleatórios entre mensagens
- ✍️ Simulação de presença ("digitando...")
- 📝 Variação de mensagens automática
- 📈 Estatísticas e monitoramento em tempo real
- 🔍 Detecção de bloqueios e pausas automáticas
- 📊 Histórico de envios (24h)
- 💾 Exportação de relatórios CSV

---

## 📋 Requisitos

- PHP 7.4 ou superior
- Composer
- ext-json
- guzzlehttp/guzzle ^7.0

---

## 📦 Instalação

```bash
composer require marksamp/evolution-api
```

Ou clone o repositório:

```bash
git clone https://github.com/seu-usuario/evolution-api-php.git
cd evolution-api-php
composer install
```

Configure o ambiente:

```bash
cp .env.example .env
nano .env
```

---

## ⚡ Guia Rápido

### Configuração Básica

```php
<?php

require_once 'vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Utils\SafeSender;

// Criar cliente
$client = new EvolutionAPIClient(
    'https://sua-evolution-api.com',
    'sua-api-key',
    'minha-instancia'
);

// Conectar automaticamente
$client->quickStart();

// Enviar mensagem
$client->sendQuickMessage('5511999999999', 'Olá! 🎉');
```

### Envio Seguro (Recomendado)

```php
// Usar SafeSender para proteção anti-bloqueio
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

$safeSender->send('5511999999999', 'Mensagem segura!');
```

📚 **Veja o [QUICKSTART.md](QUICKSTART.md) para começar em 5 minutos!**

---

## 🎯 Funcionalidades

### 1. Gerenciamento de Instâncias

```php
// Criar e conectar (método rápido)
$client->quickStart();

// Verificar se está conectado
if ($client->isConnected()) {
    echo "✅ Conectado!";
}

// Criar instância manualmente
$client->instance()->create('minha-instancia', [
    'qrcode' => true,
    'integration' => 'WHATSAPP-BAILEYS'
]);

// Conectar
$client->instance()->connect('minha-instancia');

// Status da conexão
$status = $client->instance()->getConnectionStatus('minha-instancia');

// Listar todas
$instances = $client->instance()->listAll();

// Reiniciar
$client->instance()->restart('minha-instancia');

// Deletar
$client->instance()->delete('minha-instancia');
```

### 2. Envio de Mensagens

#### Texto

```php
// Simples
$client->sendQuickMessage('5511999999999', 'Olá!');

// Completo
$client->message()->sendText('5511999999999', 'Mensagem completa', [
    'delay' => 1000
]);
```

#### Mídia

```php
// Imagem
$client->message()->sendMedia(
    '5511999999999',
    'https://exemplo.com/imagem.jpg',
    'image',
    'Legenda da imagem'
);

// Vídeo
$client->message()->sendMedia(
    '5511999999999',
    'https://exemplo.com/video.mp4',
    'video',
    'Vídeo incrível!'
);

// Documento
$client->message()->sendMedia(
    '5511999999999',
    'https://exemplo.com/doc.pdf',
    'document',
    '',
    'documento.pdf'
);
```

#### Áudio

```php
// Áudio normal
$client->message()->sendAudio(
    '5511999999999',
    'https://exemplo.com/audio.mp3',
    false
);

// PTT (Push to Talk - áudio de voz)
$client->message()->sendAudio(
    '5511999999999',
    'https://exemplo.com/audio.mp3',
    true
);
```

#### Localização

```php
$client->message()->sendLocation(
    '5511999999999',
    -3.7319,  // Latitude
    -38.5267, // Longitude
    'Fortaleza',
    'Fortaleza, Ceará, Brasil'
);
```

#### Contato

```php
$client->message()->sendContact('5511999999999', [
    [
        'fullName' => 'João Silva',
        'waid' => '5511888888888',
        'phoneNumber' => '+55 11 88888-8888'
    ]
]);
```

#### Botões Interativos

```php
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

$client->message()->sendButtons(
    '5511999999999',
    'Escolha uma opção',
    'Descrição',
    $buttons,
    'Rodapé'
);
```

#### Lista Interativa

```php
$sections = [
    [
        'title' => 'Categoria',
        'rows' => [
            [
                'rowId' => 'opt1',
                'title' => 'Opção 1',
                'description' => 'Descrição'
            ]
        ]
    ]
];

$client->message()->sendList(
    '5511999999999',
    'Título',
    'Descrição',
    'Ver Opções',
    $sections
);
```

### 3. Presença ("digitando...")

```php
// Simular digitação
$client->presence()->simulateTyping('5511999999999', 3);
$client->message()->sendText('5511999999999', 'Olá!');

// Simular gravação de áudio
$client->presence()->simulateRecording('5511999999999', 4);
$client->message()->sendAudio('5511999999999', 'audio.mp3', true);

// Controle manual
$client->presence()->typing('5511999999999', 5000);
$client->presence()->recording('5511999999999', 3000);
$client->presence()->paused('5511999999999');

// Presença global
$client->presence()->available();
$client->presence()->unavailable();

// Conversação natural
$client->presence()->simulateTyping('5511999999999', 2);
$client->message()->sendText('5511999999999', 'Primeira mensagem');
sleep(1);
$client->presence()->simulateTyping('5511999999999', 3);
$client->message()->sendText('5511999999999', 'Segunda mensagem');
```

### 4. Gerenciamento de Contatos

```php
// Listar todos
$contacts = $client->contact()->fetchAll();

// Buscar específico
$contact = $client->contact()->fetch('5511999999999');

// Verificar se existe
$exists = $client->checkNumber('5511999999999');

// Verificar múltiplos
$results = $client->contact()->checkExists([
    '5511999999999',
    '5511888888888'
]);

// Foto do perfil
$photo = $client->contact()->getProfilePicture('5511999999999');

// Bloquear/Desbloquear
$client->contact()->block('5511999999999');
$client->contact()->unblock('5511999999999');

// Atualizar seu perfil
$client->contact()->updateProfileName('Meu Nome');
$client->contact()->updateProfileStatus('Disponível');
$client->contact()->updateProfilePicture('https://exemplo.com/foto.jpg');
```

### 5. Gerenciamento de Grupos

```php
// Listar grupos
$groups = $client->group()->fetchAll();

// Criar grupo
$group = $client->group()->create(
    'Nome do Grupo',
    ['5511999999999', '5511888888888'],
    'Descrição'
);

// Informações
$info = $client->group()->getInfo('120363012345678901@g.us');

// Atualizar
$client->group()->updateSubject('120363012345678901@g.us', 'Novo Nome');
$client->group()->updateDescription('120363012345678901@g.us', 'Nova descrição');
$client->group()->updatePicture('120363012345678901@g.us', 'https://foto.jpg');

// Gerenciar participantes
$client->group()->addParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->removeParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->promoteParticipants('120363012345678901@g.us', ['5511777777777']);
$client->group()->demoteParticipants('120363012345678901@g.us', ['5511777777777']);

// Link de convite
$inviteCode = $client->group()->getInviteCode('120363012345678901@g.us');
$client->group()->revokeInviteCode('120363012345678901@g.us');

// Sair
$client->group()->leave('120363012345678901@g.us');
```

### 6. Webhooks

```php
// Configurar webhook
$client->webhook()->set(
    'https://seu-servidor.com/webhook',
    ['MESSAGES_UPSERT', 'SEND_MESSAGE', 'CONNECTION_UPDATE']
);

// Webhook global
$client->webhook()->setGlobal(
    'https://seu-servidor.com/webhook-global',
    ['MESSAGES_UPSERT']
);

// Obter configuração
$config = $client->webhook()->get();

// Remover
$client->webhook()->remove();

// Processar webhook recebido
$payload = file_get_contents('php://input');
$data = $client->webhook()->processWebhook($payload);

// Validar assinatura
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$valid = $client->webhook()->validateSignature($payload, $signature, 'secret');
```

---

## 🛡️ SafeSender - Proteção Anti-Bloqueio

O **SafeSender** é um sistema completo de proteção contra bloqueios do WhatsApp.

### Por que usar?

- ✅ **Evita bloqueios** automáticos do WhatsApp
- ✅ **Rate limiting** inteligente
- ✅ **Comportamento humanizado**
- ✅ **Estatísticas** em tempo real
- ✅ **Detecção** de problemas
- ✅ **Pausas** automáticas

### Configurações Pré-Definidas

```php
use EvolutionAPI\Utils\SafeSender;

// Para número NOVO (0-30 dias)
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroNovo()
);
// Limites: 20 msgs/hora, 50 msgs/dia, delay 5-10s

// Para número ESTABELECIDO (30+ dias)
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);
// Limites: 100 msgs/hora, 300 msgs/dia, delay 2-5s

// Para número BUSINESS
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroBusiness()
);
// Limites: 200 msgs/hora, 500 msgs/dia, delay 1-3s
```

### Uso Básico

```php
// Envio único
$result = $safeSender->send('5511999999999', 'Mensagem segura');

// Envio em lote
$destinatarios = [
    '5511999999999' => 'Olá João!',
    '5511888888888' => 'Oi Maria!',
    '5511777777777' => 'E aí Pedro!',
];

$stats = $safeSender->sendBatch($destinatarios);
$safeSender->mostrarStats();
```

### Mensagens Variadas (Anti-Spam)

```php
$numbers = ['5511999999999', '5511888888888'];

$templates = [
    'Olá {nome}! Como vai?',
    'Oi {nome}, tudo bem?',
    'E aí {nome}! Beleza?',
];

$variaveis = [
    '5511999999999' => ['nome' => 'João'],
    '5511888888888' => ['nome' => 'Maria'],
];

$stats = $safeSender->sendVariado($numbers, $templates, $variaveis);
```

### Estatísticas e Monitoramento

```php
// Obter estatísticas
$stats = $safeSender->getStats();
/*
Array (
    [enviadas] => 50
    [falhas] => 2
    [bloqueios] => 0
    [taxa_sucesso] => 96.15%
    [tempo_total] => 450
    [msgs_por_minuto] => 6.67
)
*/

// Exibir formatado
$safeSender->mostrarStats();

// Histórico
$historico = $safeSender->getHistorico(10); // Últimos 10

// Exportar CSV
$safeSender->exportarHistorico('relatorio.csv');
```

### Configuração Personalizada

```php
$configCustom = [
    'mensagens_por_minuto' => 3,
    'mensagens_por_hora' => 80,
    'mensagens_por_dia' => 250,
    'delay_minimo' => 2,
    'delay_maximo' => 6,
    'usar_presenca' => true,
    'validar_numero' => true,
    'horario_inicio' => 9,
    'horario_fim' => 21,
    'permitir_domingo' => false,
];

$safeSender = new SafeSender($client, $configCustom);
```

---

## 📊 Melhores Práticas

### Limites Seguros

| Tipo de Número | Msgs/Hora | Msgs/Dia | Delay | Contatos |
|---|---|---|---|---|
| **Novo (0-30 dias)** | 20 | 50 | 5-10s | Apenas salvos |
| **Estabelecido (30+ dias)** | 100 | 300 | 2-5s | 80% salvos |
| **Business/Verificado** | 200 | 500 | 1-3s | Mais flexível |

### ✅ SEMPRE FAÇA

- ✅ Use SafeSender para envios em massa
- ✅ Simule presença ("digitando...")
- ✅ Varie mensagens (templates)
- ✅ Valide números antes
- ✅ Respeite horários (9h-22h)
- ✅ Obtenha consentimento (opt-in)
- ✅ Monitore estatísticas
- ✅ Adicione opção de cancelamento

### ❌ NUNCA FAÇA

- ❌ Enviar para números aleatórios
- ❌ Usar mensagens idênticas
- ❌ Responder instantaneamente sempre
- ❌ Enviar à noite/madrugada
- ❌ Ignorar denúncias/bloqueios
- ❌ Usar links encurtadores suspeitos
- ❌ Exceder limites recomendados

### Exemplo de Uso Seguro

```php
use EvolutionAPI\Utils\SafeSender;

// Configurar
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

// Preparar com variação
$numbers = ['5511999999999', '5511888888888'];
$templates = [
    'Olá {nome}! Como vai?',
    'Oi {nome}, tudo bem?',
];
$vars = [
    '5511999999999' => ['nome' => 'João'],
    '5511888888888' => ['nome' => 'Maria'],
];

// Enviar
$stats = $safeSender->sendVariado($numbers, $templates, $vars);
$safeSender->mostrarStats();

// Exportar relatório
$safeSender->exportarHistorico('relatorio_' . date('Y-m-d') . '.csv');
```

---

## 💡 Exemplos

### Bot de Atendimento

```php
function botAtendimento($client, $number, $mensagem) {
    $client->presence()->available();
    sleep(1);
    
    $client->presence()->typing($number, 2000);
    sleep(2);
    
    if (stripos($mensagem, 'horário') !== false) {
        $resposta = 'Atendemos de segunda a sexta, das 9h às 18h.';
    } else {
        $resposta = 'Olá! Como posso ajudá-lo(a)?';
    }
    
    $client->message()->sendText($number, $resposta);
}
```

### Campanha em Lote

```php
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

$lotes = array_chunk($destinatarios, 50, true);

foreach ($lotes as $indice => $lote) {
    echo "Lote " . ($indice + 1) . "<br>";
    
    $stats = $safeSender->sendBatch($lote);
    $safeSender->mostrarStats();
    
    if ($indice < count($lotes) - 1) {
        sleep(300); // 5 minutos entre lotes
    }
}

$safeSender->exportarHistorico('campanha_final.csv');
```

### Sistema de Retry

```php
function enviarComRetry($safeSender, $number, $message, $max = 3) {
    for ($tentativa = 1; $tentativa <= $max; $tentativa++) {
        $resultado = $safeSender->send($number, $message);
        
        if ($resultado) {
            return true;
        }
        
        if ($tentativa < $max) {
            sleep(pow(2, $tentativa) * 60); // Exponential backoff
        }
    }
    
    return false;
}
```

---

## ❓ FAQ

### Como evitar bloqueios?

Use o SafeSender com configurações apropriadas. Para número novo: `SafeSender::configNumeroNovo()`. Para estabelecido: `SafeSender::configNumeroEstabelecido()`.

### Qual o limite seguro?

Depende: Novo (20-50/dia), Estabelecido (100-300/dia), Business (500+/dia).

### Como processar webhooks?

```php
$payload = file_get_contents('php://input');
$data = $client->webhook()->processWebhook($payload);

if ($data['event'] === 'MESSAGES_UPSERT') {
    // Processar mensagem
}
```

### Como salvar QR Code?

```php
$status = $client->instance()->getConnectionStatus($instanceName);
if (isset($status['instance']['qrcode'])) {
    $qrCode = base64_decode($status['instance']['qrcode']);
    file_put_contents('qrcode.png', $qrCode);
}
```

### Posso usar múltiplas instâncias?

Sim! Crie um cliente para cada:

```php
$client1 = new EvolutionAPIClient($url, $key, 'instancia1');
$client2 = new EvolutionAPIClient($url, $key, 'instancia2');
```

---

## 🚨 Tratamento de Exceções

```php
use EvolutionAPI\Exceptions\EvolutionAPIException;

try {
    $result = $client->message()->sendText($number, $message);
} catch (EvolutionAPIException $e) {
    echo "Erro da API: " . $e->getMessage();
    echo "Código: " . $e->getCode();
    
    if (!empty($e->getContext())) {
        print_r($e->getContext());
    }
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage();
}
```

---

## 📁 Estrutura do Projeto

```
src/
├── Config/Config.php
├── Http/HttpClient.php
├── Services/
│   ├── InstanceService.php
│   ├── MessageService.php
│   ├── ContactService.php
│   ├── GroupService.php
│   ├── WebhookService.php
│   └── PresenceService.php
├── Utils/SafeSender.php
├── Exceptions/EvolutionAPIException.php
└── EvolutionAPIClient.php
```

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Veja [CONTRIBUTING.md](CONTRIBUTING.md).

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/NovaFuncionalidade`)
3. Commit (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está licenciado sob a **Licença MIT**. Veja [LICENSE](LICENSE).

---

## ⚠️ Aviso Legal

Esta biblioteca é um cliente **não oficial** para a EvolutionAPI. Use por sua própria conta e risco.

- ⚠️ Respeite os Termos de Serviço do WhatsApp
- ⚠️ Obtenha consentimento dos usuários
- ⚠️ Use a API oficial do WhatsApp Business para uso comercial em larga escala
- ⚠️ Esta biblioteca é para fins educacionais e de desenvolvimento

**O uso inadequado pode resultar em bloqueio permanente da sua conta WhatsApp.**

---

## 📚 Recursos

- 📖 [Documentação Completa](README.md)
- ⚡ [Guia Rápido](QUICKSTART.md)
- 🤝 [Contribuindo](CONTRIBUTING.md)
- 📋 [Changelog](CHANGELOG.md)
- 🐛 [Issues](https://github.com/seu-usuario/evolution-api-php/issues)
- 💬 [Discussions](https://github.com/seu-usuario/evolution-api-php/discussions)

---

## 🙏 Agradecimentos

- [EvolutionAPI](https://evolution-api.com/)
- [Guzzle](https://github.com/guzzle/guzzle)
- Comunidade PHP

---

## 📞 Suporte

- 📧 Email: suporte@exemplo.com
- 💬 Telegram: [@seu_canal](https://t.me/seu_canal)
- 🐛 Issues: [GitHub](https://github.com/seu-usuario/evolution-api-php/issues)

---

**Desenvolvido com ❤️ para a comunidade PHP**

⭐ Se este projeto te ajudou, deixe uma estrela!

[⬆ Voltar ao topo](#-evolutionapi-php-client)
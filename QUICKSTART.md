# ⚡ Guia Rápido - EvolutionAPI PHP Client

Comece a usar a biblioteca em **menos de 5 minutos**!

---

## 📦 1. Instalação (1 minuto)

```bash
# Via Composer
composer require evolution-api/php-client

# Ou clone o repositório
git clone https://github.com/seu-usuario/evolution-api-php.git
cd evolution-api-php
composer install
```

---

## ⚙️ 2. Configuração (1 minuto)

Copie o arquivo de exemplo e configure suas credenciais:

```bash
cp .env.example .env
nano .env
```

Configure no `.env`:
```env
EVOLUTION_BASE_URL=https://sua-evolution-api.com
EVOLUTION_API_KEY=sua-chave-api
EVOLUTION_INSTANCE_NAME=minha-instancia
```

---

## 🚀 3. Primeiro Script (2 minutos)

Crie `teste.php`:

```php
<?php

require_once 'vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;

// Criar cliente
$client = new EvolutionAPIClient(
    'https://sua-evolution-api.com',
    'sua-chave-api',
    'minha-instancia'
);

// Conectar (gera QR Code automaticamente)
$client->quickStart();

// Aguardar conexão
echo "Aguardando conexão...\n";
while (!$client->isConnected()) {
    sleep(2);
    echo ".";
}

echo "\n✅ Conectado!\n";

// Enviar mensagem de teste
$result = $client->sendQuickMessage(
    '5511999999999',
    'Olá! Minha primeira mensagem via API! 🎉'
);

if ($result) {
    echo "✅ Mensagem enviada com sucesso!\n";
} else {
    echo "❌ Falha ao enviar mensagem\n";
}
```

Execute:
```bash
php teste.php
```

---

## 📱 4. Conectar WhatsApp (1 minuto)

### Método 1: CLI

```bash
php teste.php
# Escaneie o QR Code que aparecerá em qrcode.png
```

### Método 2: Interface Web

Acesse `example/qrcode.php` no navegador:
```
http://localhost/evolution/example/qrcode.php
```

Escaneie o QR Code com seu WhatsApp:
1. Abra WhatsApp no celular
2. Configurações → Aparelhos conectados
3. Conectar um aparelho
4. Escaneie o código

---

## 💬 5. Casos de Uso Comuns

### Enviar Mensagem Simples

```php
$client->sendQuickMessage('5511999999999', 'Olá!');
```

### Enviar com "Digitando..."

```php
$client->presence()->simulateTyping('5511999999999', 3);
$client->message()->sendText('5511999999999', 'Mensagem com digitação!');
```

### Enviar Imagem

```php
$client->message()->sendMedia(
    '5511999999999',
    'https://exemplo.com/imagem.jpg',
    'image',
    'Legenda da imagem'
);
```

### Enviar com Proteção Anti-Bloqueio

```php
use EvolutionAPI\Utils\SafeSender;

$safeSender = new SafeSender($client);
$safeSender->send('5511999999999', 'Mensagem protegida!');
```

### Envio em Lote Seguro

```php
use EvolutionAPI\Utils\SafeSender;

$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

$destinatarios = [
    '5511999999999' => 'Olá João!',
    '5511888888888' => 'Oi Maria!',
    '5511777777777' => 'E aí Pedro!',
];

$stats = $safeSender->sendBatch($destinatarios);
$safeSender->mostrarStats();
```

### Criar Bot Simples

```php
// No seu webhook_handler.php
$payload = file_get_contents('php://input');
$data = $client->webhook()->processWebhook($payload);

if ($data['event'] === 'MESSAGES_UPSERT') {
    foreach ($data['data']['messages'] as $msg) {
        $number = str_replace('@s.whatsapp.net', '', $msg['key']['remoteJid']);
        $text = $msg['message']['conversation'] ?? '';
        
        if (strtolower($text) === 'oi') {
            $client->presence()->simulateTyping($number, 2);
            $client->message()->sendText($number, 'Olá! Como posso ajudar?');
        }
    }
}
```

---

## 🛡️ 6. Proteção Anti-Bloqueio (IMPORTANTE!)

**SEMPRE use SafeSender para envios em massa:**

```php
use EvolutionAPI\Utils\SafeSender;

// Para número NOVO (menos de 30 dias)
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroNovo()
);

// Para número ESTABELECIDO (mais de 30 dias)
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

// Para número BUSINESS
$safeSender = new SafeSender(
    $client,
    SafeSender::configNumeroBusiness()
);
```

**Limites Seguros:**

| Tipo | Msgs/Hora | Msgs/Dia | Delay |
|------|-----------|----------|-------|
| Novo | 20 | 50 | 5-10s |
| Estabelecido | 100 | 300 | 2-5s |
| Business | 200 | 500 | 1-3s |

---

## 🎯 7. Exemplos Prontos

A biblioteca inclui vários exemplos prontos para usar:

```bash
# Exemplo básico
php example/usage.php

# Interface QR Code
php -S localhost:8000 -t example/
# Acesse: http://localhost:8000/qrcode.php

# Exemplos SafeSender
php example/safe_sender_examples.php

# Handler de Webhook
php example/webhook_handler.php
```

---

## 📚 8. Próximos Passos

1. **Leia a documentação completa**: [README.md](README.md)
2. **Veja exemplos avançados**: Pasta `example/`
3. **Configure webhooks**: Para receber mensagens
4. **Implemente SafeSender**: Para evitar bloqueios
5. **Monitore métricas**: Use dashboard de estatísticas

---

## 🆘 9. Problemas Comuns

### Erro: "Class not found"
```bash
# Solução: Instalar dependências
composer install
composer dump-autoload
```

### QR Code não aparece
```bash
# Verificar se instância foi criada
php -r "require 'vendor/autoload.php'; 
use EvolutionAPI\EvolutionAPIClient;
\$c = new EvolutionAPIClient('url','key','inst');
print_r(\$c->instance()->listAll());"
```

### Mensagem não envia
```php
// Verificar conexão primeiro
if (!$client->isConnected()) {
    echo "Não conectado! Escaneie QR Code.\n";
}

// Validar número
if (!$client->checkNumber('5511999999999')) {
    echo "Número inválido!\n";
}
```

### Rate Limit / Bloqueio
```php
// SEMPRE use SafeSender
use EvolutionAPI\Utils\SafeSender;

$safeSender = new SafeSender($client);
// Respeita limites automaticamente
```

---

## 💡 10. Dicas Importantes

### ✅ FAÇA
- ✅ Use SafeSender para envios em massa
- ✅ Simule presença ("digitando...")
- ✅ Valide números antes de enviar
- ✅ Respeite horários (9h-22h)
- ✅ Varie mensagens (use templates)
- ✅ Monitore estatísticas
- ✅ Obtenha consentimento dos usuários

### ❌ NÃO FAÇA
- ❌ Enviar spam ou mensagens não solicitadas
- ❌ Usar mensagens idênticas
- ❌ Enviar à noite (22h-8h)
- ❌ Exceder limites recomendados
- ❌ Ignorar erros e bloqueios
- ❌ Usar links suspeitos
- ❌ Enviar sem delay entre mensagens

---

## 📊 11. Checklist de Produção

Antes de colocar em produção, verifique:

- [ ] Testes em ambiente de desenvolvimento
- [ ] SafeSender configurado e ativo
- [ ] Limites apropriados definidos
- [ ] Webhooks configurados
- [ ] Sistema de logs ativo
- [ ] Monitoramento de métricas
- [ ] Backup de configurações
- [ ] Tratamento de erros implementado
- [ ] Validação de números ativa
- [ ] Opt-in/opt-out implementado
- [ ] Política de privacidade clara
- [ ] Termos de uso aceitos pelos usuários

---

## 🎓 12. Recursos de Aprendizado

### Documentação
- 📖 [README Completo](README.md)
- 🛡️ [Guia Anti-Bloqueio](README.md#️-proteção-anti-bloqueio)
- ❓ [FAQ](README.md#-faq)
- 🤝 [Como Contribuir](CONTRIBUTING.md)

### Exemplos
- 💻 [Exemplos Básicos](example/usage.php)
- 🔐 [SafeSender](example/safe_sender_examples.php)
- 📱 [QR Code](example/qrcode.php)
- 🔔 [Webhooks](example/webhook_handler.php)

### Comunidade
- 💬 [Discussions](https://github.com/seu-usuario/evolution-api-php/discussions)
- 🐛 [Issues](https://github.com/seu-usuario/evolution-api-php/issues)
- 📧 Email: suporte@exemplo.com

---

## 🚀 13. Modelo de Script Completo

Script pronto para usar em produção:

```php
<?php

require_once 'vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Utils\SafeSender;
use EvolutionAPI\Exceptions\EvolutionAPIException;

// Carregar variáveis de ambiente
$baseUrl = getenv('EVOLUTION_BASE_URL');
$apiKey = getenv('EVOLUTION_API_KEY');
$instanceName = getenv('EVOLUTION_INSTANCE_NAME');

try {
    // Criar cliente
    $client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);
    
    // Verificar conexão
    if (!$client->isConnected()) {
        throw new Exception('WhatsApp não conectado. Escaneie QR Code.');
    }
    
    // Criar SafeSender
    $safeSender = new SafeSender(
        $client,
        SafeSender::configNumeroEstabelecido()
    );
    
    // Seus números e mensagens
    $destinatarios = [
        '5511999999999' => 'Olá! Mensagem personalizada.',
        // Adicione mais aqui
    ];
    
    // Enviar em lote com proteção
    $stats = $safeSender->sendBatch($destinatarios, function($number, $result, $atual, $total) {
        if ($result) {
            echo "✅ [{$atual}/{$total}] Enviado: {$number}\n";
        } else {
            echo "❌ [{$atual}/{$total}] Falhou: {$number}\n";
        }
    });
    
    // Exibir estatísticas
    $safeSender->mostrarStats();
    
    // Exportar relatório
    $arquivo = 'relatorio_' . date('Y-m-d_H-i-s') . '.csv';
    $safeSender->exportarHistorico($arquivo);
    echo "📊 Relatório exportado: {$arquivo}\n";
    
    // Resultado final
    echo "\n🎉 Processo concluído!\n";
    echo "✅ Enviadas: {$stats['enviadas']}\n";
    echo "❌ Falhas: {$stats['falhas']}\n";
    echo "📈 Taxa: {$stats['taxa_sucesso']}\n";
    
} catch (EvolutionAPIException $e) {
    echo "❌ Erro da API: {$e->getMessage()}\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Erro: {$e->getMessage()}\n";
    exit(1);
}
```

Salve como `envio_producao.php` e execute:
```bash
php envio_producao.php
```

---

## 📞 14. Suporte

Precisa de ajuda?

- 📚 **Documentação**: [README.md](README.md)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/seu-usuario/evolution-api-php/discussions)
- 🐛 **Bugs**: [GitHub Issues](https://github.com/seu-usuario/evolution-api-php/issues)
- 📧 **Email**: suporte@exemplo.com
- 💬 **Telegram**: [@seu_canal](https://t.me/seu_canal)

---

## 🎉 Parabéns!

Você está pronto para usar a biblioteca! 🚀

**Próximos Passos:**
1. Teste em ambiente de desenvolvimento
2. Implemente SafeSender
3. Configure webhooks
4. Monitore métricas
5. Coloque em produção

**Lembre-se:**
- ✅ Sempre use SafeSender
- ✅ Respeite limites
- ✅ Varie mensagens
- ✅ Monitore estatísticas
- ✅ Obtenha consentimento

---

**Feito com ❤️ para facilitar sua vida**

[📚 Documentação](README.md) • [🐛 Issues](https://github.com/seu-usuario/evolution-api-php/issues) • [💬 Discussions](https://github.com/seu-usuario/evolution-api-php/discussions)

[⬆ Voltar ao topo](#-guia-rápido---evolutionapi-php-client)
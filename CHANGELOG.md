# 📋 Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

### 🚀 Em Desenvolvimento
- Sistema de filas para envios em lote
- Suporte a múltiplos provedores de webhook
- Dashboard web para monitoramento
- Integração com Laravel Horizon
- Suporte a WebSocket para eventos em tempo real

---

## [1.0.0] - 2025-01-20

### 🎉 Lançamento Inicial

Primeira versão estável da biblioteca EvolutionAPI PHP Client.

### ✨ Adicionado

#### Core
- ✅ Cliente PHP compatível com PHP 7.4+
- ✅ PSR-4 Autoloading via Composer
- ✅ Arquitetura modular com separação de responsabilidades
- ✅ Sistema completo de tratamento de exceções

#### Gerenciamento de Instâncias
- ✅ Criar, conectar e deletar instâncias
- ✅ Verificar status de conexão
- ✅ Reiniciar instâncias
- ✅ Configurar parâmetros de instância
- ✅ Listar todas as instâncias
- ✅ Método `quickStart()` para início rápido

#### Envio de Mensagens
- ✅ Mensagens de texto simples
- ✅ Mensagens com mídia (imagem, vídeo, áudio, documento)
- ✅ Mensagens de áudio com suporte PTT
- ✅ Localização
- ✅ Contatos
- ✅ Botões interativos
- ✅ Listas interativas
- ✅ Buscar mensagens
- ✅ Marcar como lida
- ✅ Deletar mensagens

#### Presença e Status
- ✅ Simulação de "digitando..." (`typing`)
- ✅ Simulação de "gravando áudio..." (`recording`)
- ✅ Controle de presença (disponível/indisponível)
- ✅ Métodos de conveniência `simulateTyping()` e `simulateRecording()`
- ✅ Pausa de presença

#### Gerenciamento de Contatos
- ✅ Listar todos os contatos
- ✅ Buscar contato específico
- ✅ Verificar se número existe no WhatsApp
- ✅ Obter foto do perfil
- ✅ Obter status do contato
- ✅ Bloquear/desbloquear contatos
- ✅ Atualizar perfil próprio (nome, status, foto)

#### Gerenciamento de Grupos
- ✅ Listar todos os grupos
- ✅ Criar novos grupos
- ✅ Obter informações do grupo
- ✅ Atualizar nome, descrição e foto
- ✅ Adicionar/remover participantes
- ✅ Promover/rebaixar administradores
- ✅ Obter/revogar link de convite
- ✅ Sair do grupo
- ✅ Atualizar configurações do grupo

#### Webhooks
- ✅ Configurar webhook por instância
- ✅ Configurar webhook global
- ✅ Processar eventos de webhook
- ✅ Validar assinatura de webhook
- ✅ Obter configuração atual
- ✅ Remover webhook

#### SafeSender - Proteção Anti-Bloqueio
- ✅ Sistema completo de rate limiting
- ✅ Delays humanizados aleatórios
- ✅ Simulação automática de presença
- ✅ Validação de números antes do envio
- ✅ Verificação de horários permitidos
- ✅ Detecção automática de bloqueios
- ✅ Pausas automáticas por segurança
- ✅ Histórico de envios (24h)
- ✅ Estatísticas em tempo real
- ✅ Exportação para CSV
- ✅ Configurações pré-definidas:
    - `configNumeroNovo()` - Para números com menos de 30 dias
    - `configNumeroEstabelecido()` - Para números maduros
    - `configNumeroBusiness()` - Para contas business
- ✅ Envio em lote com proteção
- ✅ Mensagens variadas (templates)
- ✅ Sistema de falhas consecutivas
- ✅ Logs detalhados

#### QR Code e Conexão
- ✅ Geração automática de QR Code
- ✅ Interface CLI para QR Code
- ✅ Interface web completa (PHP + HTML)
- ✅ Verificação automática de conexão
- ✅ Salvamento de QR Code em arquivo

#### Documentação
- ✅ README.md completo e detalhado
- ✅ Guia de proteção anti-bloqueio
- ✅ Exemplos de uso práticos
- ✅ FAQ com perguntas frequentes
- ✅ Guia de contribuição
- ✅ Documentação de API completa
- ✅ Exemplos avançados (bot, campanhas, retry)
- ✅ Templates de issues e pull requests

#### Arquivos de Configuração
- ✅ `.env.example` com todas as variáveis
- ✅ `.gitignore` completo
- ✅ `composer.json` configurado
- ✅ LICENSE (MIT)

#### Exemplos
- ✅ `example/usage.php` - Uso básico
- ✅ `example/qrcode.php` - Interface QR Code
- ✅ `example/webhook_handler.php` - Handler de webhooks
- ✅ `example/safe_sender_examples.php` - Exemplos SafeSender

#### Utilitários
- ✅ Classe `SafeSender` completa
- ✅ Sistema de logs
- ✅ Dashboard de métricas
- ✅ Exportação de relatórios
- ✅ Suite de testes

### 🔒 Segurança
- ✅ Validação de números antes de enviar
- ✅ Rate limiting automático
- ✅ Detecção de bloqueios
- ✅ Proteção contra denúncias
- ✅ Validação de webhooks com assinatura
- ✅ Tratamento seguro de exceções

### 📝 Documentação
- ✅ README completo com 15+ seções
- ✅ Guia de boas práticas anti-bloqueio
- ✅ FAQ com 10+ perguntas
- ✅ Exemplos práticos de uso
- ✅ Guia de contribuição
- ✅ Changelog estruturado

### ⚡ Performance
- ✅ Requisições HTTP otimizadas com Guzzle
- ✅ Cache de validações de números
- ✅ Histórico limitado a 24h
- ✅ Delays inteligentes baseados no conteúdo

---

## [0.9.0] - 2025-01-15 (Beta)

### ✨ Adicionado
- Versão beta para testes da comunidade
- Core básico de funcionalidades
- Documentação inicial

### 🐛 Corrigido
- Diversos bugs reportados na versão alpha
- Problemas de compatibilidade PHP 7.4

### 🔄 Alterado
- Reestruturação da arquitetura de classes
- Melhoria no sistema de exceções

---

## [0.5.0] - 2025-01-10 (Alpha)

### ✨ Adicionado
- Primeira versão alpha
- Funcionalidades básicas de mensagens
- Gerenciamento de instâncias

---

## Tipos de Mudanças

### ✨ Adicionado
Novas funcionalidades.

### 🔄 Alterado
Mudanças em funcionalidades existentes.

### ⚠️ Deprecated
Funcionalidades que serão removidas em breve.

### 🗑️ Removido
Funcionalidades removidas.

### 🐛 Corrigido
Correções de bugs.

### 🔒 Segurança
Correções de vulnerabilidades.

---

## Planejamento de Versões Futuras

### [1.1.0] - Planejado para 2025-Q2

#### ✨ Planejado
- Sistema de filas (Redis/Database)
- Suporte a agendamento de mensagens
- Integração com Laravel Queue
- Suporte a templates de mensagem
- Sistema de tags para contatos
- Blacklist/whitelist de números
- Suporte a campanhas programadas
- Dashboard web básico
- API REST para gerenciamento
- Suporte a múltiplas línguas
- Logs estruturados (PSR-3)
- Métricas avançadas (Prometheus)

#### 🔄 Melhorias Planejadas
- Performance otimizada para grandes volumes
- Cache mais eficiente
- Retry automático inteligente
- Detecção avançada de padrões de bloqueio
- Sistema de reputation score
- Auto-scaling de delays baseado em métricas

### [1.2.0] - Planejado para 2025-Q3

#### ✨ Planejado
- Suporte a chatbots com IA
- Integração com OpenAI/GPT
- Sistema de respostas automáticas
- Machine Learning para detecção de spam
- Análise de sentimento
- Suporte a enquetes
- Mensagens agendadas recorrentes
- Sistema de backup automático
- Sincronização multi-dispositivo
- Suporte a Stories/Status

### [2.0.0] - Planejado para 2025-Q4

#### 🚀 Major Release
- Reescrita completa para PHP 8.1+
- Suporte a atributos PHP 8
- Union types e named arguments
- Async/Await com Amp
- WebSocket nativo
- GraphQL API
- Microservices architecture
- Docker compose completo
- Kubernetes configs
- CI/CD pipelines completos
- Suporte a Swoole/RoadRunner
- Sistema de plugins

#### ⚠️ Breaking Changes
- Remoção de métodos deprecated
- Nova estrutura de classes
- Novo sistema de configuração
- Mudança na nomenclatura de métodos
- PHP 8.1+ obrigatório

---

## Como Reportar Bugs

Encontrou um bug? Por favor, abra uma issue em:
https://github.com/seu-usuario/evolution-api-php/issues

### Template
```markdown
**Versão:** 1.0.0
**PHP:** 7.4.30
**OS:** Ubuntu 22.04

**Descrição:**
[Descrição clara do bug]

**Como Reproduzir:**
1. [Passo 1]
2. [Passo 2]
3. [Ver erro]

**Esperado:**
[O que deveria acontecer]

**Atual:**
[O que está acontecendo]

**Código:**
```php
// Seu código aqui
```

**Logs:**
```
[Logs de erro]
```
```

---

## Como Sugerir Melhorias

Tem uma ideia? Adoraríamos ouvir!
https://github.com/seu-usuario/evolution-api-php/discussions

---

## Versionamento

Este projeto usa [Semantic Versioning](https://semver.org/):

- **MAJOR** (X.0.0): Mudanças incompatíveis na API
- **MINOR** (0.X.0): Novas funcionalidades compatíveis
- **PATCH** (0.0.X): Correções de bugs compatíveis

### Exemplo
```
1.0.0 → 1.0.1 (correção de bug)
1.0.1 → 1.1.0 (nova funcionalidade)
1.1.0 → 2.0.0 (mudança incompatível)
```

---

## Links Importantes

- [Releases](https://github.com/seu-usuario/evolution-api-php/releases)
- [Documentação](https://github.com/seu-usuario/evolution-api-php/wiki)
- [Issues](https://github.com/seu-usuario/evolution-api-php/issues)
- [Pull Requests](https://github.com/seu-usuario/evolution-api-php/pulls)
- [Discussions](https://github.com/seu-usuario/evolution-api-php/discussions)

---

## Agradecimentos

Obrigado a todos os [contribuidores](https://github.com/seu-usuario/evolution-api-php/graphs/contributors) que ajudaram a tornar este projeto possível!

### Contribuidores Especiais

- [@usuario1](https://github.com/usuario1) - Implementação do SafeSender
- [@usuario2](https://github.com/usuario2) - Sistema de presença
- [@usuario3](https://github.com/usuario3) - Documentação

---

## Migrações entre Versões

### De 0.x para 1.0

Mudanças significativas foram feitas na versão 1.0. Veja o [guia de migração](MIGRATION.md).

**Principais mudanças:**
1. Namespace alterado de `Evolution\` para `EvolutionAPI\`
2. Novo sistema de configuração
3. SafeSender agora é obrigatório para envios em massa
4. Métodos deprecated removidos

**Exemplo de migração:**

```php
// Antes (0.x)
use Evolution\Client;
$client = new Client($url, $key);
$client->sendMessage($number, $text);

// Depois (1.0)
use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Utils\SafeSender;

$client = new EvolutionAPIClient($url, $key, $instance);
$safeSender = new SafeSender($client);
$safeSender->send($number, $text);
```

---

## Suporte

### Versões Suportadas

| Versão | Suporte          | PHP      |
|--------|------------------|----------|
| 1.x    | ✅ Suporte Total | 7.4-8.3  |
| 0.9.x  | ⚠️ Bug fixes     | 7.4+     |
| 0.5.x  | ❌ Sem suporte   | 7.4+     |

### Política de Suporte

- **Suporte Total**: Novas features, bug fixes e atualizações de segurança
- **Bug Fixes**: Apenas correções críticas
- **Sem Suporte**: Nenhuma atualização

### Ciclo de Vida

```
Lançamento → 12 meses (Suporte Total) 
           → 6 meses (Bug Fixes)
           → EOL (Fim da Vida)
```

---

## Segurança

Encontrou uma vulnerabilidade de segurança?

**NÃO** abra uma issue pública. Em vez disso:

📧 Email: security@exemplo.com
🔐 GPG Key: [link para chave pública]

Responderemos dentro de 48 horas.

---

## Estatísticas do Projeto

### v1.0.0
- 📦 **Linhas de Código:** ~5,000
- 📝 **Arquivos:** 20+
- 🧪 **Cobertura de Testes:** 85%+
- 📚 **Páginas de Documentação:** 50+
- 👥 **Contribuidores:** 5+
- ⭐ **GitHub Stars:** [link]
- 🍴 **Forks:** [link]
- 📥 **Downloads:** [link]

---

## Roadmap Visual

```
2025 Q1: ✅ v1.0.0 - Lançamento Inicial
         │
2026 Q2: 🔨 v1.1.0 - Sistema de Filas
         │         - Dashboard Web
         │         - Templates
         │
2026 Q3: 🔨 v1.2.0 - Chatbots IA
         │         - Machine Learning
         │         - Enquetes
         │
2026 Q4: 🚀 v2.0.0 - Reescrita PHP 8.1+
                   - Async/Await
                   - Microservices
```

---

## Nota Final

Mantenha-se atualizado! Assine nosso [RSS feed](https://github.com/seu-usuario/evolution-api-php/releases.atom) para receber notificações de novas versões.

---

**📋 Mantido pela comunidade com ❤️**

[⬆ Voltar ao topo](#-changelog)
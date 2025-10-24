# 🤝 Guia de Contribuição

Obrigado por considerar contribuir com o **EvolutionAPI PHP Client**! Este documento fornece diretrizes para contribuir com o projeto.

## 📋 Índice

- [Código de Conduta](#código-de-conduta)
- [Como Contribuir](#como-contribuir)
- [Reportando Bugs](#reportando-bugs)
- [Sugerindo Melhorias](#sugerindo-melhorias)
- [Processo de Pull Request](#processo-de-pull-request)
- [Padrões de Código](#padrões-de-código)
- [Estrutura de Commits](#estrutura-de-commits)
- [Testes](#testes)

---

## 📜 Código de Conduta

Este projeto adere a um código de conduta. Ao participar, espera-se que você mantenha este código. Por favor, reporte comportamentos inaceitáveis.

### Nossos Padrões

**Comportamentos esperados:**
- ✅ Usar linguagem acolhedora e inclusiva
- ✅ Respeitar pontos de vista diferentes
- ✅ Aceitar críticas construtivas
- ✅ Focar no que é melhor para a comunidade
- ✅ Mostrar empatia com outros membros

**Comportamentos inaceitáveis:**
- ❌ Linguagem ou imagens sexualizadas
- ❌ Trolling, insultos ou comentários depreciativos
- ❌ Assédio público ou privado
- ❌ Publicar informações privadas de terceiros
- ❌ Conduta não profissional

---

## 🚀 Como Contribuir

Existem várias formas de contribuir:

### 1. Reportando Bugs
Encontrou um bug? Ajude-nos a melhorar!

### 2. Sugerindo Melhorias
Tem uma ideia para melhorar o projeto? Adoraríamos ouvir!

### 3. Documentação
Melhorias na documentação são sempre bem-vindas.

### 4. Código
Contribuições de código são o coração do projeto!

### 5. Testes
Adicionar ou melhorar testes aumenta a qualidade.

### 6. Exemplos
Novos exemplos de uso ajudam a comunidade.

---

## 🐛 Reportando Bugs

Antes de criar um issue:

1. **Verifique** se o bug já foi reportado
2. **Teste** na versão mais recente
3. **Colete** informações sobre o erro

### Template de Bug Report

```markdown
**Descrição do Bug**
Uma descrição clara e concisa do que é o bug.

**Como Reproduzir**
Passos para reproduzir o comportamento:
1. Configure '...'
2. Execute '...'
3. Veja o erro

**Comportamento Esperado**
O que você esperava que acontecesse.

**Comportamento Atual**
O que realmente aconteceu.

**Screenshots**
Se aplicável, adicione screenshots.

**Ambiente:**
- OS: [ex: Ubuntu 22.04]
- PHP Version: [ex: 7.4.30]
- Library Version: [ex: 1.0.0]
- EvolutionAPI Version: [ex: 2.0.0]

**Código de Exemplo**
```php
// Código que reproduz o problema
```

**Logs de Erro**
```
Erros relevantes aqui
```

**Contexto Adicional**
Qualquer outra informação relevante.
```

---

## 💡 Sugerindo Melhorias

### Template de Feature Request

```markdown
**A melhoria está relacionada a um problema?**
Uma descrição clara do problema. Ex: Sempre fico frustrado quando [...]

**Descreva a solução que você gostaria**
Uma descrição clara e concisa do que você quer que aconteça.

**Descreva alternativas consideradas**
Alternativas que você considerou.

**Contexto Adicional**
Qualquer outro contexto ou screenshots sobre a solicitação.

**Você está disposto a trabalhar nesta feature?**
[ ] Sim, posso implementar
[ ] Sim, mas preciso de ajuda
[ ] Não, apenas sugerindo
```

---

## 🔄 Processo de Pull Request

### Antes de Enviar

1. ✅ Leia a documentação
2. ✅ Verifique issues existentes
3. ✅ Teste suas mudanças
4. ✅ Atualize a documentação
5. ✅ Siga os padrões de código

### Passo a Passo

#### 1. Fork o Projeto

```bash
# Clone seu fork
git clone https://github.com/seu-usuario/evolution-api-php.git
cd evolution-api-php
```

#### 2. Crie uma Branch

```bash
# Crie uma branch descritiva
git checkout -b feature/nova-funcionalidade
# ou
git checkout -b fix/correcao-bug
```

Nomenclatura de branches:
- `feature/nome-da-feature` - Novas funcionalidades
- `fix/nome-do-bug` - Correções de bugs
- `docs/descricao` - Melhorias na documentação
- `refactor/descricao` - Refatorações
- `test/descricao` - Adição/melhoria de testes

#### 3. Faça suas Mudanças

```bash
# Faça commits atômicos e descritivos
git add .
git commit -m "feat: adiciona suporte a mensagens de áudio"
```

#### 4. Mantenha Atualizado

```bash
# Adicione o repositório original como remote
git remote add upstream https://github.com/original/evolution-api-php.git

# Atualize sua branch
git fetch upstream
git rebase upstream/main
```

#### 5. Execute Testes

```bash
# Instale dependências
composer install

# Execute testes
composer test

# Verifique estilo de código
composer cs-check
```

#### 6. Push e Pull Request

```bash
# Envie para seu fork
git push origin feature/nova-funcionalidade
```

Então abra um Pull Request no GitHub com:
- Título descritivo
- Descrição detalhada das mudanças
- Referências a issues relacionados
- Screenshots se aplicável

### Template de Pull Request

```markdown
## Descrição
Descrição clara das mudanças realizadas.

## Motivação e Contexto
Por que esta mudança é necessária? Que problema resolve?

## Como foi testado?
Descreva como você testou suas mudanças.

## Tipos de mudanças
- [ ] Bug fix (mudança que corrige um issue)
- [ ] Nova feature (mudança que adiciona funcionalidade)
- [ ] Breaking change (mudança que quebra compatibilidade)
- [ ] Documentação

## Checklist
- [ ] Meu código segue o estilo do projeto
- [ ] Revisei meu próprio código
- [ ] Comentei código complexo
- [ ] Atualizei a documentação
- [ ] Minhas mudanças não geram novos warnings
- [ ] Adicionei testes
- [ ] Todos os testes passam
- [ ] Atualizei o CHANGELOG.md

## Issues Relacionados
Closes #123
Related to #456
```

---

## 📝 Padrões de Código

### PSR-12

Este projeto segue a **PSR-12** (Extended Coding Style).

```php
<?php

namespace EvolutionAPI\Services;

use EvolutionAPI\Http\HttpClient;
use EvolutionAPI\Config\Config;

class ExampleService
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
     * Método de exemplo
     * @param string $param
     * @return array
     */
    public function exampleMethod(string $param): array
    {
        // Código aqui
        return [];
    }
}
```

### Regras Importantes

1. **Indentação**: 4 espaços (sem tabs)
2. **Linha**: Máximo 120 caracteres
3. **Nomenclatura**:
    - Classes: `PascalCase`
    - Métodos: `camelCase`
    - Variáveis: `camelCase`
    - Constantes: `UPPER_CASE`
4. **Documentação**: PHPDoc para classes e métodos públicos
5. **Tipos**: Use type hints sempre que possível
6. **Retorno**: Sempre declare tipo de retorno

### Exemplo de Documentação

```php
/**
 * Envia mensagem de texto com proteção anti-bloqueio
 * 
 * @param string $number Número no formato internacional (5511999999999)
 * @param string $message Mensagem a ser enviada
 * @param array $options Opções adicionais
 * @return array|false Retorna array com resultado ou false em caso de falha
 * @throws EvolutionAPIException Em caso de erro da API
 */
public function send(string $number, string $message, array $options = [])
{
    // Implementação
}
```

---

## 📦 Estrutura de Commits

Use **Conventional Commits** para mensagens de commit:

### Formato

```
<tipo>(<escopo>): <descrição curta>

<corpo opcional>

<rodapé opcional>
```

### Tipos

- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Mudanças na documentação
- `style`: Formatação, ponto e vírgula, etc
- `refactor`: Refatoração de código
- `test`: Adição ou correção de testes
- `chore`: Manutenção, build, etc

### Exemplos

```bash
# Nova funcionalidade
git commit -m "feat(message): adiciona suporte a mensagens de vídeo"

# Correção de bug
git commit -m "fix(safesender): corrige contagem de envios no período"

# Documentação
git commit -m "docs(readme): atualiza exemplos de uso do SafeSender"

# Refatoração
git commit -m "refactor(http): melhora tratamento de erros"

# Teste
git commit -m "test(presence): adiciona testes para presença"

# Breaking change
git commit -m "feat(client)!: remove método deprecated sendMessage"
```

---

## 🧪 Testes

### Executando Testes

```bash
# Instalar dependências de desenvolvimento
composer install

# Executar todos os testes
composer test

# Executar com cobertura
composer test-coverage

# Executar testes específicos
./vendor/bin/phpunit tests/Services/MessageServiceTest.php
```

### Escrevendo Testes

```php
<?php

namespace EvolutionAPI\Tests\Services;

use PHPUnit\Framework\TestCase;
use EvolutionAPI\Services\MessageService;

class MessageServiceTest extends TestCase
{
    private $messageService;
    
    protected function setUp(): void
    {
        // Setup antes de cada teste
        $this->messageService = new MessageService(
            $this->createMock(HttpClient::class),
            $this->createMock(Config::class)
        );
    }
    
    public function testSendText(): void
    {
        // Arrange
        $number = '5511999999999';
        $message = 'Test message';
        
        // Act
        $result = $this->messageService->sendText($number, $message);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('key', $result);
    }
}
```

### Cobertura de Testes

Almejamos manter **mínimo 80% de cobertura** de código.

---

## 🎯 Áreas que Precisam de Ajuda

Estamos especialmente procurando ajuda com:

- 📝 Melhorias na documentação
- 🧪 Adição de testes
- 🐛 Correção de bugs reportados
- 🌍 Traduções
- 💡 Novos exemplos de uso
- 🔒 Melhorias de segurança

---

## 📧 Dúvidas?

Se tiver dúvidas sobre como contribuir:

- 💬 Abra uma [Discussion](https://github.com/seu-usuario/evolution-api-php/discussions)
- 📧 Entre em contato: contribuicoes@exemplo.com
- 📚 Consulte a [documentação](https://github.com/seu-usuario/evolution-api-php/wiki)

---

## 🙏 Agradecimentos

Obrigado por contribuir! Cada contribuição, grande ou pequena, faz diferença.

### Principais Contribuidores

<!-- Lista será gerada automaticamente -->

---

## 📜 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a [Licença MIT](LICENSE).

---

**Feito com ❤️ pela comunidade**

[⬆ Voltar ao topo](#-guia-de-contribuição)
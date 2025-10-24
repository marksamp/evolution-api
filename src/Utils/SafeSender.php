<?php

namespace EvolutionAPI\Utils;

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

/**
 * Classe para envio seguro de mensagens com proteção anti-bloqueio
 */
class SafeSender
{
    /** @var EvolutionAPIClient */
    private $client;

    /** @var array */
    private $config;

    /** @var array */
    private $stats;

    /** @var array */
    private $historico;

    public function __construct(EvolutionAPIClient $client, array $config = [])
    {
        $this->client = $client;

        // Configurações padrão (conservadoras)
        $this->config = array_merge([
            'mensagens_por_minuto' => 2,
            'mensagens_por_hora' => 50,
            'mensagens_por_dia' => 200,
            'delay_minimo' => 3,
            'delay_maximo' => 8,
            'usar_presenca' => true,
            'validar_numero' => true,
            'horario_inicio' => 8,
            'horario_fim' => 22,
            'permitir_domingo' => false,
            'max_falhas_consecutivas' => 3,
            'tempo_pausa_apos_falha' => 300, // 5 minutos
        ], $config);

        $this->stats = [
            'enviadas' => 0,
            'falhas' => 0,
            'bloqueios' => 0,
            'inicio' => time(),
            'falhas_consecutivas' => 0,
        ];

        $this->historico = [];
    }

    /**
     * Envia mensagem com todas as proteções
     * @param string $number
     * @param string $message
     * @param array $options
     * @return array|false
     */
    public function send(string $number, string $message, array $options = [])
    {
        try {
            // 1. Verificações de segurança
            if (!$this->podeEnviar()) {
                $this->log('⚠️ Limite de envios atingido. Aguardando...');
                return false;
            }

            if (!$this->horarioPermitido()) {
                $this->log('⏰ Fora do horário permitido');
                return false;
            }

            // 2. Validar número
            if ($this->config['validar_numero']) {
                if (!$this->validarNumero($number)) {
                    $this->log("❌ Número inválido: {$number}");
                    $this->stats['falhas']++;
                    $this->stats['falhas_consecutivas']++;
                    return false;
                }
            }

            // 3. Delay antes de enviar
            $this->aguardarDelay();

            // 4. Simular presença humana
            if ($this->config['usar_presenca']) {
                $tempoDigitacao = $this->calcularTempoDigitacao($message);
                $this->client->presence()->simulateTyping($number, $tempoDigitacao);
            }

            // 5. Enviar mensagem
            $result = $this->client->message()->sendText($number, $message);

            // 6. Registrar sucesso
            $this->stats['enviadas']++;
            $this->stats['falhas_consecutivas'] = 0; // Resetar falhas consecutivas
            $this->registrarHistorico($number, $message, true);
            $this->log("✅ Enviada para {$number}");

            return $result;

        } catch (EvolutionAPIException $e) {
            $this->stats['falhas']++;
            $this->stats['falhas_consecutivas']++;
            $this->registrarHistorico($number, $message, false, $e->getMessage());
            $this->log("❌ Erro ao enviar: " . $e->getMessage());

            // Verificar se é bloqueio
            if ($this->isBloqueio($e)) {
                $this->stats['bloqueios']++;
                $this->log("🚨 ALERTA: Possível bloqueio detectado!");
            }

            // Verificar se deve pausar por muitas falhas
            if ($this->stats['falhas_consecutivas'] >= $this->config['max_falhas_consecutivas']) {
                $this->pausarPorFalhas();
            }

            return false;
        } catch (\Exception $e) {
            $this->stats['falhas']++;
            $this->stats['falhas_consecutivas']++;
            $this->log("❌ Erro geral: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia mensagens em lote com segurança
     * @param array $destinatarios ['numero' => 'mensagem']
     * @param callable|null $callback Função callback para cada envio
     * @return array Estatísticas do envio
     */
    public function sendBatch(array $destinatarios, callable $callback = null): array
    {
        $total = count($destinatarios);
        $contador = 0;

        $this->log("📤 Iniciando envio em lote: {$total} mensagens");

        foreach ($destinatarios as $number => $message) {
            $contador++;

            $this->log("[{$contador}/{$total}] Processando {$number}...");

            // Enviar com proteção
            $result = $this->send($number, $message);

            // Callback personalizado
            if ($callback) {
                $callback($number, $result, $contador, $total);
            }

            // Verificar se deve pausar
            if ($this->devePausar()) {
                $this->pausarEnvios();
            }
        }

        return $this->getStats();
    }

    /**
     * Envia mensagens com variação de templates
     * @param array $numbers
     * @param array $templates
     * @param array $variaveis
     * @return array
     */
    public function sendVariado(array $numbers, array $templates, array $variaveis = []): array
    {
        $destinatarios = [];

        foreach ($numbers as $number) {
            // Escolher template aleatório
            $template = $templates[array_rand($templates)];

            // Substituir variáveis
            $message = $this->processarTemplate($template, $variaveis[$number] ?? []);

            // Adicionar variação extra
            $message = $this->adicionarVariacao($message);

            $destinatarios[$number] = $message;
        }

        return $this->sendBatch($destinatarios);
    }

    /**
     * Verifica se pode enviar mensagem agora
     * @return bool
     */
    private function podeEnviar(): bool
    {
        $agora = time();

        // Contar envios nos últimos períodos
        $enviosUltimoMinuto = $this->contarEnviosNosPeriodo(60);
        $enviosUltimaHora = $this->contarEnviosNosPeriodo(3600);
        $enviosHoje = $this->contarEnviosNosPeriodo(86400);

        // Verificar limites
        if ($enviosUltimoMinuto >= $this->config['mensagens_por_minuto']) {
            $this->log("⏳ Limite por minuto atingido ({$enviosUltimoMinuto}/{$this->config['mensagens_por_minuto']})");
            return false;
        }

        if ($enviosUltimaHora >= $this->config['mensagens_por_hora']) {
            $this->log("⏳ Limite por hora atingido ({$enviosUltimaHora}/{$this->config['mensagens_por_hora']})");
            return false;
        }

        if ($enviosHoje >= $this->config['mensagens_por_dia']) {
            $this->log("⏳ Limite diário atingido ({$enviosHoje}/{$this->config['mensagens_por_dia']})");
            return false;
        }

        return true;
    }

    /**
     * Conta envios bem-sucedidos em um período
     * @param int $segundos
     * @return int
     */
    private function contarEnviosNosPeriodo(int $segundos): int
    {
        $agora = time();
        $timestampLimite = $agora - $segundos;
        $contador = 0;

        foreach ($this->historico as $registro) {
            if ($registro['timestamp'] >= $timestampLimite && $registro['sucesso']) {
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Registra envio no histórico
     * @param string $number
     * @param string $message
     * @param bool $sucesso
     * @param string $erro
     * @return void
     */
    private function registrarHistorico(string $number, string $message, bool $sucesso, string $erro = ''): void
    {
        $this->historico[] = [
            'numero' => $number,
            'mensagem' => substr($message, 0, 50), // Primeiros 50 caracteres
            'sucesso' => $sucesso,
            'erro' => $erro,
            'timestamp' => time(),
            'data' => date('Y-m-d H:i:s'),
        ];

        // Limpar histórico antigo (manter últimas 24h)
        $this->limparHistoricoAntigo();
    }

    /**
     * Remove registros antigos do histórico
     * @return void
     */
    private function limparHistoricoAntigo(): void
    {
        $limite = time() - 86400; // 24 horas

        $this->historico = array_filter($this->historico, function($registro) use ($limite) {
            return $registro['timestamp'] >= $limite;
        });

        // Reindexar array
        $this->historico = array_values($this->historico);
    }

    /**
     * Verifica se está em horário permitido
     * @return bool
     */
    private function horarioPermitido(): bool
    {
        $hora = (int)date('H');
        $diaSemana = (int)date('N');

        // Verificar horário
        if ($hora < $this->config['horario_inicio'] || $hora > $this->config['horario_fim']) {
            return false;
        }

        // Verificar domingo
        if (!$this->config['permitir_domingo'] && $diaSemana === 7) {
            return false;
        }

        return true;
    }

    /**
     * Valida número antes de enviar
     * @param string $number
     * @return bool
     */
    private function validarNumero(string $number): bool
    {
        try {
            return $this->client->checkNumber($number);
        } catch (\Exception $e) {
            $this->log("⚠️ Erro ao validar número: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Aguarda delay aleatório entre mensagens
     * @return void
     */
    private function aguardarDelay(): void
    {
        $delay = rand($this->config['delay_minimo'], $this->config['delay_maximo']);
        $this->log("⏱️ Aguardando {$delay} segundos...");
        sleep($delay);
    }

    /**
     * Calcula tempo de digitação baseado no tamanho da mensagem
     * @param string $message
     * @return int Segundos
     */
    private function calcularTempoDigitacao(string $message): int
    {
        // Simular 40 palavras por minuto (200 chars/min)
        $caracteres = strlen($message);
        $segundos = ($caracteres / 200) * 60;

        // Limitar entre 2-8 segundos
        return max(2, min(8, (int)$segundos));
    }

    /**
     * Verifica se deve pausar os envios
     * @return bool
     */
    private function devePausar(): bool
    {
        // Pausar se taxa de erro for alta (mais de 30%)
        if ($this->stats['enviadas'] > 0) {
            $taxaErro = ($this->stats['falhas'] / ($this->stats['enviadas'] + $this->stats['falhas'])) * 100;

            if ($taxaErro > 30) {
                $this->log("⚠️ Taxa de erro alta: " . round($taxaErro, 2) . "%");
                return true;
            }
        }

        // Pausar se detectar bloqueios
        if ($this->stats['bloqueios'] > 0) {
            $this->log("🚨 Bloqueios detectados: " . $this->stats['bloqueios']);
            return true;
        }

        // Pausar se muitas falhas consecutivas
        if ($this->stats['falhas_consecutivas'] >= $this->config['max_falhas_consecutivas']) {
            $this->log("🚨 Muitas falhas consecutivas: " . $this->stats['falhas_consecutivas']);
            return true;
        }

        return false;
    }

    /**
     * Pausa envios por segurança
     * @return void
     */
    private function pausarEnvios(): void
    {
        $this->log('🚨 PAUSANDO ENVIOS POR SEGURANÇA!');
        $this->log('📊 Estatísticas: ' . json_encode($this->stats));

        // Pausar por 1 hora
        $tempoPausa = 3600;
        $this->log("⏰ Aguardando {$tempoPausa} segundos (1 hora) antes de continuar...");
        sleep($tempoPausa);

        // Resetar contadores de falha
        $this->stats['falhas_consecutivas'] = 0;
        $this->log('✅ Pausa concluída. Retomando envios...');
    }

    /**
     * Pausa por falhas consecutivas
     * @return void
     */
    private function pausarPorFalhas(): void
    {
        $tempoPausa = $this->config['tempo_pausa_apos_falha'];
        $this->log("⚠️ Pausando por {$tempoPausa} segundos devido a falhas consecutivas...");
        sleep($tempoPausa);
        $this->stats['falhas_consecutivas'] = 0;
    }

    /**
     * Verifica se exceção indica bloqueio
     * @param EvolutionAPIException $e
     * @return bool
     */
    private function isBloqueio(EvolutionAPIException $e): bool
    {
        $message = strtolower($e->getMessage());

        $indicadores = [
            'blocked',
            'banned',
            'spam',
            'forbidden',
            '403',
            'not authorized',
            'violation',
        ];

        foreach ($indicadores as $indicador) {
            if (strpos($message, $indicador) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Processa template com variáveis
     * @param string $template
     * @param array $variaveis
     * @return string
     */
    private function processarTemplate(string $template, array $variaveis): string
    {
        foreach ($variaveis as $chave => $valor) {
            $template = str_replace('{' . $chave . '}', $valor, $template);
        }

        return $template;
    }

    /**
     * Adiciona variações para evitar mensagens idênticas
     * @param string $message
     * @return string
     */
    private function adicionarVariacao(string $message): string
    {
        // Emojis aleatórios (com moderação)
        $emojis = ['😊', '👋', '✨', '🎉', '💡', ''];
        $emoji = $emojis[array_rand($emojis)];

        // Adicionar emoji ocasionalmente (50% de chance)
        if (rand(0, 1) && !empty($emoji)) {
            $message .= ' ' . $emoji;
        }

        // Variações de pontuação
        $pontuacoes = ['.', '!', ''];
        if (!preg_match('/[.!?]$/', $message)) {
            $message .= $pontuacoes[array_rand($pontuacoes)];
        }

        return $message;
    }

    /**
     * Retorna estatísticas dos envios
     * @return array
     */
    public function getStats(): array
    {
        $tempoTotal = time() - $this->stats['inicio'];
        $totalMensagens = $this->stats['enviadas'] + $this->stats['falhas'];
        $taxaSucesso = $totalMensagens > 0
            ? (($this->stats['enviadas']) / $totalMensagens) * 100
            : 0;

        return [
            'enviadas' => $this->stats['enviadas'],
            'falhas' => $this->stats['falhas'],
            'bloqueios' => $this->stats['bloqueios'],
            'falhas_consecutivas' => $this->stats['falhas_consecutivas'],
            'taxa_sucesso' => round($taxaSucesso, 2) . '%',
            'tempo_total' => $tempoTotal,
            'msgs_por_minuto' => $tempoTotal > 0 ? round($this->stats['enviadas'] / ($tempoTotal / 60), 2) : 0,
        ];
    }

    /**
     * Exibe estatísticas formatadas
     * @return void
     */
    public function mostrarStats(): void
    {
        $stats = $this->getStats();

        echo "<br>";
        echo "╔════════════════════════════════════════╗<br>";
        echo "║     ESTATÍSTICAS DE ENVIO SEGURO       ║<br>";
        echo "╠════════════════════════════════════════╣<br>";
        echo sprintf("║ ✅ Enviadas:       %18d ║<br>", $stats['enviadas']);
        echo sprintf("║ ❌ Falhas:         %18d ║<br>", $stats['falhas']);
        echo sprintf("║ 🚫 Bloqueios:      %18d ║<br>", $stats['bloqueios']);
        echo sprintf("║ ⚠️  Falhas Consec: %18d ║<br>", $stats['falhas_consecutivas']);
        echo sprintf("║ 📊 Taxa Sucesso:   %18s ║<br>", $stats['taxa_sucesso']);
        echo sprintf("║ ⏱️ Tempo Total:    %17ds ║<br>", $stats['tempo_total']);
        echo sprintf("║ 📈 Msgs/Minuto:    %18s ║<br>", $stats['msgs_por_minuto']);
        echo "╚════════════════════════════════════════╝<br>";
        echo "<br>";
    }

    /**
     * Obtém histórico de envios
     * @param int|null $ultimos Número de registros (null = todos)
     * @return array
     */
    public function getHistorico(int $ultimos = null): array
    {
        if ($ultimos === null) {
            return $this->historico;
        }

        return array_slice($this->historico, -$ultimos);
    }

    /**
     * Exporta histórico para arquivo
     * @param string $arquivo
     * @return bool
     */
    public function exportarHistorico(string $arquivo): bool
    {
        $conteudo = "Data,Numero,Mensagem,Sucesso,Erro<br>";

        foreach ($this->historico as $registro) {
            $conteudo .= sprintf(
                "%s,%s,\"%s\",%s,\"%s\"<br>",
                $registro['data'],
                $registro['numero'],
                str_replace('"', '""', $registro['mensagem']),
                $registro['sucesso'] ? 'Sim' : 'Não',
                str_replace('"', '""', $registro['erro'])
            );
        }

        return file_put_contents($arquivo, $conteudo) !== false;
    }

    /**
     * Configura limites personalizados
     * @param array $limites
     * @return void
     */
    public function setLimites(array $limites): void
    {
        $this->config = array_merge($this->config, $limites);
    }

    /**
     * Obtém configuração atual
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Reseta estatísticas
     * @return void
     */
    public function resetStats(): void
    {
        $this->stats = [
            'enviadas' => 0,
            'falhas' => 0,
            'bloqueios' => 0,
            'inicio' => time(),
            'falhas_consecutivas' => 0,
        ];
    }

    /**
     * Limpa histórico
     * @return void
     */
    public function limparHistorico(): void
    {
        $this->historico = [];
    }

    /**
     * Log de atividades
     * @param string $message
     * @return void
     */
    private function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[{$timestamp}] {$message}<br>";

        // Opcional: salvar em arquivo
        // file_put_contents('safe_sender.log', "[{$timestamp}] {$message}<br>", FILE_APPEND);
    }

    /**
     * Cria configuração para número novo (mais restritiva)
     * @return array
     */
    public static function configNumeroNovo(): array
    {
        return [
            'mensagens_por_minuto' => 1,
            'mensagens_por_hora' => 20,
            'mensagens_por_dia' => 50,
            'delay_minimo' => 5,
            'delay_maximo' => 10,
            'usar_presenca' => true,
            'validar_numero' => true,
            'max_falhas_consecutivas' => 2,
        ];
    }

    /**
     * Cria configuração para número estabelecido (menos restritiva)
     * @return array
     */
    public static function configNumeroEstabelecido(): array
    {
        return [
            'mensagens_por_minuto' => 3,
            'mensagens_por_hora' => 100,
            'mensagens_por_dia' => 300,
            'delay_minimo' => 2,
            'delay_maximo' => 5,
            'usar_presenca' => true,
            'validar_numero' => true,
            'max_falhas_consecutivas' => 3,
        ];
    }

    /**
     * Cria configuração para número verificado/business
     * @return array
     */
    public static function configNumeroBusiness(): array
    {
        return [
            'mensagens_por_minuto' => 5,
            'mensagens_por_hora' => 200,
            'mensagens_por_dia' => 500,
            'delay_minimo' => 1,
            'delay_maximo' => 3,
            'usar_presenca' => true,
            'validar_numero' => false,
            'max_falhas_consecutivas' => 5,
        ];
    }
}
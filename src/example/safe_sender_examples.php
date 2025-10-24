<?php

require_once __DIR__ . '/vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Utils\SafeSender;

$baseUrl = 'https://sua-evolution-api.com';
$apiKey = 'sua-chave-api';
$instanceName = 'minha-instancia';

$client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);

echo "=== Exemplos de Uso - SafeSender ===<br><br>";

// ========================================
// EXEMPLO 1: Envio Único Seguro
// ========================================
echo "1. Envio único com proteção:<br>";

// Criar SafeSender com configuração padrão
$safeSender = new SafeSender($client);

// Enviar mensagem única
$resultado = $safeSender->send(
    '5511999999999',
    'Olá! Esta é uma mensagem enviada com proteção anti-bloqueio.'
);

if ($resultado) {
    echo "✅ Mensagem enviada com sucesso!<br><br>";
} else {
    echo "❌ Falha ao enviar mensagem<br><br>";
}


// ========================================
// EXEMPLO 2: Envio em Lote
// ========================================
echo "2. Envio em lote com proteção:<br>";

$destinatarios = [
    '5511999999999' => 'Olá João! Como vai?',
    '5511888888888' => 'Oi Maria! Tudo bem?',
    '5511777777777' => 'E aí Pedro! Beleza?',
];

$stats = $safeSender->sendBatch($destinatarios, function($number, $result, $atual, $total) {
    if ($result) {
        echo "  ✅ [{$atual}/{$total}] Enviado para {$number}<br>";
    } else {
        echo "  ❌ [{$atual}/{$total}] Falha ao enviar para {$number}<br>";
    }
});

echo "<br>📊 Estatísticas do lote:<br>";
print_r($stats);
echo "<br>";


// ========================================
// EXEMPLO 3: Mensagens Variadas (Anti-Spam)
// ========================================
echo "3. Envio com variação de templates:<br>";

$numbers = [
    '5511999999999',
    '5511888888888',
    '5511777777777',
];

$templates = [
    'Olá {nome}! Temos uma novidade para você!',
    'Oi {nome}, como vai? Queria te contar uma novidade.',
    'E aí {nome}! Preparamos algo especial.',
    '{nome}, tudo bem? Tenho uma novidade!',
];

$variaveis = [
    '5511999999999' => ['nome' => 'João'],
    '5511888888888' => ['nome' => 'Maria'],
    '5511777777777' => ['nome' => 'Pedro'],
];

$stats = $safeSender->sendVariado($numbers, $templates, $variaveis);

echo "📊 Resultado:<br>";
$safeSender->mostrarStats();


// ========================================
// EXEMPLO 4: Configuração para Número Novo
// ========================================
echo "4. Configuração para número novo (mais cauteloso):<br>";

$safeSenderNovo = new SafeSender(
    $client,
    SafeSender::configNumeroNovo()
);

echo "⚙️ Configuração aplicada:<br>";
print_r($safeSenderNovo->getConfig());
echo "<br>";

$resultado = $safeSenderNovo->send(
    '5511999999999',
    'Primeira mensagem do número novo - modo seguro!'
);

echo "<br>";


// ========================================
// EXEMPLO 5: Configuração para Número Estabelecido
// ========================================
echo "5. Configuração para número estabelecido:<br>";

$safeSenderEstabelecido = new SafeSender(
    $client,
    SafeSender::configNumeroEstabelecido()
);

$destinatarios = [];
for ($i = 1; $i <= 10; $i++) {
    $destinatarios["551199999999{$i}"] = "Mensagem #{$i} - Número estabelecido";
}

$stats = $safeSenderEstabelecido->sendBatch($destinatarios);

$safeSenderEstabelecido->mostrarStats();


// ========================================
// EXEMPLO 6: Configuração Personalizada
// ========================================
echo "6. Configuração personalizada:<br>";

$configPersonalizada = [
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

$safeSenderCustom = new SafeSender($client, $configPersonalizada);

echo "⚙️ Configuração personalizada aplicada!<br><br>";


// ========================================
// EXEMPLO 7: Envio com Callback Personalizado
// ========================================
echo "7. Envio com callback e logging avançado:<br>";

$destinatarios = [
    '5511999999999' => 'Teste 1',
    '5511888888888' => 'Teste 2',
    '5511777777777' => 'Teste 3',
];

$logs = [];

$stats = $safeSender->sendBatch($destinatarios, function($number, $result, $atual, $total) use (&$logs) {
    $status = $result ? 'sucesso' : 'falha';

    $logs[] = [
        'numero' => $number,
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s'),
        'progresso' => "{$atual}/{$total}",
    ];

    echo "  📝 Log: {$number} - {$status}<br>";
});

echo "<br>📋 Logs completos:<br>";
print_r($logs);
echo "<br>";


// ========================================
// EXEMPLO 8: Campanha Completa com Pausa
// ========================================
echo "8. Campanha completa com múltiplos lotes:<br>";

function executarCampanha($client, $todosDestinatarios) {
    $safeSender = new SafeSender(
        $client,
        SafeSender::configNumeroEstabelecido()
    );

    // Dividir em lotes de 50
    $lotes = array_chunk($todosDestinatarios, 50, true);
    $totalLotes = count($lotes);

    echo "📦 Campanha dividida em {$totalLotes} lotes<br><br>";

    foreach ($lotes as $indiceLote => $lote) {
        $numeroLote = $indiceLote + 1;
        echo "🚀 Processando lote {$numeroLote}/{$totalLotes}...<br>";

        $stats = $safeSender->sendBatch($lote);

        echo "<br>📊 Estatísticas do lote {$numeroLote}:<br>";
        $safeSender->mostrarStats();

        // Pausa entre lotes (se não for o último)
        if ($numeroLote < $totalLotes) {
            echo "⏸️ Pausando 5 minutos antes do próximo lote...<br><br>";
            sleep(300); // 5 minutos
        }
    }

    echo "🎉 Campanha concluída!<br>";
    return $safeSender->getStats();
}

// Exemplo de uso (comentado para não executar)
/*
$todosDestinatarios = [];
for ($i = 1; $i <= 150; $i++) {
    $todosDestinatarios["551199999999{$i}"] = "Mensagem da campanha #{$i}";
}

$resultadoCampanha = executarCampanha($client, $todosDestinatarios);
*/


// ========================================
// EXEMPLO 9: Sistema de Retry com Exponential Backoff
// ========================================
echo "9. Sistema de retry inteligente:<br>";

function enviarComRetry($safeSender, $number, $message, $maxTentativas = 3) {
    $tentativa = 0;

    while ($tentativa < $maxTentativas) {
        $tentativa++;
        echo "Tentativa {$tentativa}/{$maxTentativas} para {$number}...<br>";

        $resultado = $safeSender->send($number, $message);

        if ($resultado) {
            echo "✅ Sucesso na tentativa {$tentativa}!<br>";
            return true;
        }

        if ($tentativa < $maxTentativas) {
            // Exponential backoff: 2^tentativa minutos
            $aguardar = pow(2, $tentativa) * 60;
            echo "⏳ Aguardando {$aguardar} segundos antes de tentar novamente...<br>";
            sleep($aguardar);
        }
    }

    echo "❌ Falhou após {$maxTentativas} tentativas<br>";
    return false;
}

// Testar retry
enviarComRetry($safeSender, '5511999999999', 'Mensagem com retry', 3);
echo "<br>";


// ========================================
// EXEMPLO 10: Monitoramento e Alertas
// ========================================
echo "10. Sistema de monitoramento com alertas:<br>";

function monitorarEnvios($safeSender, $destinatarios) {
    $alertas = [];

    $stats = $safeSender->sendBatch($destinatarios, function($number, $result, $atual, $total) use (&$alertas, $safeSender) {
        $stats = $safeSender->getStats();

        // Calcular taxa de falha
        $taxaFalha = $stats['enviadas'] > 0
            ? ($stats['falhas'] / $stats['enviadas']) * 100
            : 0;

        // Alertas
        if ($taxaFalha > 30) {
            $alertas[] = "🚨 ALERTA: Taxa de falha em {$taxaFalha}%!";
        }

        if ($stats['bloqueios'] > 0) {
            $alertas[] = "🚫 ALERTA CRÍTICO: Bloqueio detectado!";
        }
    });

    if (!empty($alertas)) {
        echo "<br>⚠️ ALERTAS DETECTADOS:<br>";
        foreach ($alertas as $alerta) {
            echo "  {$alerta}<br>";
        }
    } else {
        echo "<br>✅ Nenhum alerta - Operação saudável!<br>";
    }

    return $stats;
}


// ========================================
// RESUMO FINAL
// ========================================
echo "<br>";
echo "╔════════════════════════════════════════════════╗<br>";
echo "║          RESUMO DE BOAS PRÁTICAS              ║<br>";
echo "╠════════════════════════════════════════════════╣<br>";
echo "║ ✅ Use SafeSender para todos os envios        ║<br>";
echo "║ ✅ Configure limites apropriados               ║<br>";
echo "║ ✅ Varie mensagens (templates)                 ║<br>";
echo "║ ✅ Valide números antes de enviar              ║<br>";
echo "║ ✅ Use presença ('digitando...')               ║<br>";
echo "║ ✅ Monitore estatísticas constantemente        ║<br>";
echo "║ ✅ Pause se detectar problemas                 ║<br>";
echo "║ ✅ Respeite horários comerciais                ║<br>";
echo "║ ✅ Divida em lotes pequenos                    ║<br>";
echo "║ ✅ Implemente sistema de retry                 ║<br>";
echo "╚════════════════════════════════════════════════╝<br>";
echo "<br>";

echo "🎯 SafeSender está pronto para uso!<br>";
echo "📚 Consulte o guia completo de boas práticas para mais detalhes.<br>";
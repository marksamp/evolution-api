<?php

// Verificar se autoloader existe
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Erro: Autoloader não encontrado!<br>Execute: <code>composer install</code>');
}

// Incluir autoloader do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Verificar se classe existe após autoloader
if (!class_exists('EvolutionAPI\EvolutionAPIClient')) {
    die('Erro: Classe EvolutionAPIClient não encontrada!<br>Verifique se os arquivos estão na pasta src/');
}

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

try {
    echo "<h2>Teste da EvolutionAPI PHP Client</h2>";

    // Configurações da API
    $baseUrl = 'https://apievolution.euregistro.com.br';
    $apiKey = '21F7488FCB70-476A-9D98-92C03325EBFD';
    $instanceName = 'EuRegistro';

    // Criar cliente
    $client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);

    echo "<p>✅ Cliente criado com sucesso!</p>";
    echo "<p>🔧 URL Base: {$baseUrl}</p>";
    echo "<p>📱 Instância: {$instanceName}</p>";

    // Teste básico - listar instâncias
    echo "<h3>Testando conexão com a API...</h3>";

    $instances = $client->instance()->listAll();
    echo "<p>✅ Conexão com API funcionando!</p>";
    echo "<p>📋 Instâncias encontradas: " . count($instances) . "</p>";

} catch (EvolutionAPIException $e) {
    echo "<p>❌ Erro da EvolutionAPI: " . $e->getMessage() . "</p>";
    echo "<p>🔍 Código: " . $e->getCode() . "</p>";
    if (!empty($e->getContext())) {
        echo "<p>📋 Contexto: " . json_encode($e->getContext()) . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro geral: " . $e->getMessage() . "</p>";
    echo "<p>📁 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>Informações de Debug:</h3>";
echo "<p>📁 Diretório atual: " . __DIR__ . "</p>";
echo "<p>🐘 Versão PHP: " . PHP_VERSION . "</p>";
echo "<p>📦 Autoloader: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? '✅ Encontrado' : '❌ Não encontrado') . "</p>";
echo "<p>🎯 Classe principal: " . (class_exists('EvolutionAPI\EvolutionAPIClient') ? '✅ Carregada' : '❌ Não carregada') . "</p>";
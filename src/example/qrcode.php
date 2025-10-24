<?php

require_once __DIR__ . '/vendor/autoload.php';

use EvolutionAPI\EvolutionAPIClient;
use EvolutionAPI\Exceptions\EvolutionAPIException;

// Configurações
$baseUrl = 'https://sua-evolution-api.com';
$apiKey = 'sua-chave-api';
$instanceName = 'minha-instancia';

$client = new EvolutionAPIClient($baseUrl, $apiKey, $instanceName);

// Processar requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'create_instance':
                $result = $client->instance()->create($instanceName, [
                    'qrcode' => true,
                    'integration' => 'WHATSAPP-BAILEYS'
                ]);

                $connection = $client->instance()->connect($instanceName);

                echo json_encode(['success' => true, 'data' => $result]);
                break;

            case 'get_qrcode':
                $status = $client->instance()->getConnectionStatus($instanceName);

                if (isset($status['instance']['qrcode'])) {
                    echo json_encode([
                        'success' => true,
                        'qrcode' => $status['instance']['qrcode'],
                        'state' => $status['instance']['state'] ?? 'unknown'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'QR Code não disponível',
                        'state' => $status['instance']['state'] ?? 'unknown'
                    ]);
                }
                break;

            case 'check_connection':
                $isConnected = $client->isConnected();
                $status = $client->instance()->getConnectionStatus($instanceName);

                echo json_encode([
                    'success' => true,
                    'connected' => $isConnected,
                    'state' => $status['instance']['state'] ?? 'unknown'
                ]);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        }

    } catch (EvolutionAPIException $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EvolutionAPI - Conectar WhatsApp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #25d366, #128c7e);
        }

        .header {
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: #25d366;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .status {
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .status.waiting {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .status.connected {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .qr-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .qr-code {
            max-width: 250px;
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .loading {
            width: 50px;
            height: 50px;
            border: 3px solid #e3f2fd;
            border-top: 3px solid #25d366;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .instructions {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            text-align: left;
        }

        .instructions h3 {
            color: #1565c0;
            margin-bottom: 15px;
            text-align: center;
            font-size: 1.2rem;
        }

        .instructions ol {
            color: #333;
            line-height: 1.8;
            padding-left: 20px;
        }

        .instructions li {
            margin: 10px 0;
            position: relative;
        }

        .btn {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .success-animation {
            font-size: 4rem;
            animation: bounce 1s ease infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(0px); }
            to { transform: translateY(-10px); }
        }

        .footer {
            margin-top: 30px;
            color: #888;
            font-size: 0.9rem;
        }

        .instance-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">📱</div>
        <h1>WhatsApp Connect</h1>
        <p>Conecte sua conta ao EvolutionAPI</p>
        <div class="instance-info">
            Instância: <strong><?php echo htmlspecialchars($instanceName); ?></strong>
        </div>
    </div>

    <div id="status" class="status waiting">
        ⏳ Preparando conexão...
    </div>

    <div class="qr-container" id="qrContainer">
        <div class="loading"></div>
        <p style="margin-top: 20px; color: #666;">Aguarde...</p>
    </div>

    <div class="instructions">
        <h3>📋 Como conectar:</h3>
        <ol>
            <li><strong>Abra o WhatsApp</strong> no seu celular</li>
            <li>Vá em <strong>Configurações</strong> → <strong>Aparelhos conectados</strong></li>
            <li>Toque em <strong>"Conectar um aparelho"</strong></li>
            <li><strong>Escaneie o QR Code</strong> exibido acima</li>
        </ol>
    </div>

    <div>
        <button class="btn" onclick="startConnection()" id="startBtn">
            🚀 Iniciar Conexão
        </button>
        <button class="btn" onclick="checkConnection()" id="checkBtn" style="display: none;">
            🔍 Verificar Status
        </button>
    </div>

    <div class="footer">
        <p>Powered by EvolutionAPI</p>
    </div>
</div>

<script>
    let checkInterval;
    let connected = false;

    async function makeRequest(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);

        for (const key in data) {
            formData.append(key, data[key]);
        }

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            return await response.json();
        } catch (error) {
            console.error('Erro na requisição:', error);
            throw error;
        }
    }

    async function startConnection() {
        const statusEl = document.getElementById('status');
        const qrContainer = document.getElementById('qrContainer');
        const startBtn = document.getElementById('startBtn');
        const checkBtn = document.getElementById('checkBtn');

        try {
            startBtn.disabled = true;
            statusEl.className = 'status waiting';
            statusEl.innerHTML = '🔄 Criando instância...';

            qrContainer.innerHTML = '<div class="loading"></div><p style="margin-top: 20px; color: #666;">Configurando conexão...</p>';

            // Criar instância
            const createResult = await makeRequest('create_instance');

            if (!createResult.success) {
                throw new Error(createResult.message || 'Erro ao criar instância');
            }

            statusEl.innerHTML = '🔍 Aguardando QR Code...';

            // Aguardar QR Code
            setTimeout(getQRCode, 3000);

        } catch (error) {
            statusEl.className = 'status error';
            statusEl.innerHTML = '❌ Erro: ' + error.message;
            qrContainer.innerHTML = '<p style="color: #dc3545;">Erro ao conectar</p>';
            startBtn.disabled = false;
        }
    }

    async function getQRCode() {
        if (connected) return;

        try {
            const result = await makeRequest('get_qrcode');

            if (result.success && result.qrcode) {
                displayQRCode(result.qrcode);

                // Iniciar verificação periódica
                if (!checkInterval) {
                    checkInterval = setInterval(checkConnection, 3000);
                }

                document.getElementById('checkBtn').style.display = 'inline-block';

            } else if (result.state === 'open') {
                // Já conectado
                showConnected();
            } else {
                // Aguardar mais um pouco
                setTimeout(getQRCode, 2000);
            }

        } catch (error) {
            console.error('Erro ao obter QR Code:', error);
            setTimeout(getQRCode, 5000);
        }
    }

    function displayQRCode(qrCodeData) {
        const qrContainer = document.getElementById('qrContainer');
        const statusEl = document.getElementById('status');

        qrContainer.innerHTML = `
                <img src="data:image/png;base64,${qrCodeData}"
                     alt="QR Code"
                     class="qr-code">
                <p style="margin-top: 15px; color: #666;">QR Code válido por 60 segundos</p>
            `;

        statusEl.className = 'status waiting';
        statusEl.innerHTML = '📱 Escaneie o QR Code com seu WhatsApp';

        document.getElementById('startBtn').disabled = false;
        document.getElementById('startBtn').innerHTML = '🔄 Gerar Novo QR Code';
    }

    async function checkConnection() {
        if (connected) return;

        try {
            const result = await makeRequest('check_connection');

            if (result.success && result.connected) {
                showConnected();
            } else if (result.state === 'close') {
                const statusEl = document.getElementById('status');
                statusEl.innerHTML = '⚠️ QR Code expirou - Gere um novo';
            }

        } catch (error) {
            console.error('Erro ao verificar conexão:', error);
        }
    }

    function showConnected() {
        connected = true;
        clearInterval(checkInterval);

        const statusEl = document.getElementById('status');
        const qrContainer = document.getElementById('qrContainer');

        statusEl.className = 'status connected';
        statusEl.innerHTML = '✅ Conectado com sucesso!';

        qrContainer.innerHTML = `
                <div style="padding: 40px;">
                    <div class="success-animation">🎉</div>
                    <h3 style="color: #28a745; margin: 20px 0;">Conexão Estabelecida!</h3>
                    <p style="color: #666;">Sua instância está pronta para enviar mensagens.</p>
                    <div style="margin-top: 20px; padding: 15px; background: #e8f5e8; border-radius: 10px;">
                        <strong>Próximos passos:</strong><br>
                        • Use a API para enviar mensagens<br>
                        • Configure webhooks se necessário<br>
                        • Gerencie contatos e grupos
                    </div>
                </div>
            `;

        document.getElementById('startBtn').style.display = 'none';
        document.getElementById('checkBtn').style.display = 'none';
    }

    // Auto-iniciar quando a página carregar
    window.onload = function() {
        // Verificar se já está conectado primeiro
        checkConnection().then(() => {
            if (!connected) {
                // Se não estiver conectado, mostrar botão para iniciar
                document.getElementById('qrContainer').innerHTML = `
                        <div style="padding: 40px;">
                            <div style="font-size: 3rem; margin-bottom: 20px;">📱</div>
                            <h3 style="color: #666;">Clique para conectar</h3>
                            <p style="color: #888;">Sua instância WhatsApp será configurada</p>
                        </div>
                    `;
                document.getElementById('status').innerHTML = '⚡ Pronto para conectar';
            }
        });
    };

    // Limpar intervalo ao sair da página
    window.onbeforeunload = function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    };
</script>
</body>
</html>
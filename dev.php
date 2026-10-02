<?php

// Define o fuso horário e uma variável de status
$atualizacao = 'FUNCIONAL ATÉ HOJE 29-09-21';
error_reporting(0); // Oculta erros

/**
 * Função para extrair uma string entre dois delimitadores
 */
function getStr($string, $start, $end) {
    $str = explode($start, $string);
    if (isset($str[1])) {
        $str = explode($end, $str[1]);
        return $str[0];
    }
    return '';
}

// --- Dados Fornecidos ---
// Credenciais exatas que você mencionou
$login_fixo = "244933308070";
$senha_fixa = "Montanadev@@12";

// Tokens fornecidos (usados como fallback caso o getStr não encontre)
$token_client_padrao = "4a481dc0-0181-4df7-aa07-6c0bde36cd7f";
$token_xsrf_padrao = "e1cf1d79-8ee8-5f47-8f61-96b28ca0b547";
$device_sah_padrao = "8cf77efd8506ba828c3567f5606bb66a";

// --- Simulação de Resposta Anterior para o getStr ---
// Em um cenário real, você faria um curl GET antes para pegar esses dados.
// Aqui, criamos uma string falsa apenas para demonstrar o uso do getStr.
$resposta_simulada = '{"status":"ok", "x-client-token":"' . $token_client_padrao . '", "x-xsrf-token-users":"' . $token_xsrf_padrao . '", "x-sah-device":"' . $device_sah_padrao . '"}';

// Extraindo os tokens usando getStr (como solicitado)
$x_client_token = getStr($resposta_simulada, '"x-client-token":"', '"');
$x_xsrf_token_users = getStr($resposta_simulada, '"x-xsrf-token-users":"', '"');
$x_sah_device = getStr($resposta_simulada, '"x-sah-device":"', '"');

// Se o getStr falhar, usa os padrões
if (empty($x_client_token)) $x_client_token = $token_client_padrao;
if (empty($x_xsrf_token_users)) $x_xsrf_token_users = $token_xsrf_padrao;
if (empty($x_sah_device)) $x_sah_device = $device_sah_padrao;


/**
 * Função responsável por testar o login
 */
function Testar($login, $senha, $x_client_token, $x_xsrf_token_users, $x_sah_device) {
    // Define o caminho do cookie
    $cookieFile = getcwd() . "/cookies.txt";

    // Remove o arquivo de cookie anterior, se existir
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }

    // --- Montagem do Payload JSON ---
    // Usando exatamente as credenciais fornecidas
    $payload = json_encode([
        "login" => $login,
        "password" => $senha
    ]);

    // --- Montagem dos Headers ---
    $headers = [
        "accept: application/json",
        "content-type: application/json",
        "referer: https://www.premierbet.co.ao/",
        'sec-ch-ua: "Google Chrome";v="153", "Not_A Brand";v="8", "Chromium";v="153"',
        "sec-ch-ua-mobile: ?0",
        'sec-ch-ua-platform: "Windows"',
        "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36",
        "x-client-token: " . $x_client_token,
        "x-sah-device: " . $x_sah_device,
        "x-xsrf-token-users: " . $x_xsrf_token_users
    ];

    // Inicializa o cURL
    $ch = curl_init();

    // Configura as opções do cURL
    curl_setopt_array($ch, [
        // IMPORTANTE: Substitua pela URL exata do endpoint de login
        CURLOPT_URL => 'https://users-api.premierbet.co.ao/v1/auth/login?country=AO&group=g2&platform=desktop&locale=pt', 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_TIMEOUT => 30
    ]);

    // Executa a requisição
    $resultado = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // --- Exibição do Resultado ---
    echo "Testando: $login | $senha\n";
    echo "HTTP Code: $http_code\n";
    echo "Resposta: $resultado\n";
    echo "----------------------------------------\n";
}

// --- Execução ---
// Chama a função Testar com as credenciais fixas que você forneceu
Testar($login_fixo, $senha_fixa, $x_client_token, $x_xsrf_token_users, $x_sah_device);

?>

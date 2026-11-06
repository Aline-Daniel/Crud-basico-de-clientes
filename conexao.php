<?php
// Variáveis obrigatórias no servidor: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME.
// Nenhuma credencial de produção fica no repositório.
function db_env($nome) {
    $valor = getenv($nome);
    if ($valor === false || $valor === '') {
        http_response_code(503);
        exit('Banco de dados não configurado.');
    }
    return $valor;
}
function h($valor) {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verificar_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Formulário inválido. Atualize a página.');
    }
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli(db_env('DB_HOST'), db_env('DB_USER'), db_env('DB_PASSWORD'), db_env('DB_NAME'), (int) (getenv('DB_PORT') ?: 3306));
    $mysqli->set_charset('utf8mb4');
} catch (mysqli_sql_exception $erro) {
    error_log('Erro de conexão MySQL no CRUD de clientes.');
    http_response_code(503);
    exit('Não foi possível conectar ao banco de dados.');
}
function formatar_data($data) {
    if (!$data) return '';
    return implode('/', array_reverse(explode('-', $data)));
}
function formatar_telefone($telefone) {
    if (!$telefone) return '';
    $digitos = preg_replace('/\D/', '', $telefone);
    if (strlen($digitos) !== 11) return $telefone;
    return '(' . substr($digitos, 0, 2) . ') ' . substr($digitos, 2, 5) . '-' . substr($digitos, 7);
}

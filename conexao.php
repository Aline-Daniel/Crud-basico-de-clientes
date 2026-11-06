<?php
declare(strict_types=1);
require_once __DIR__ . '/seguranca.php';
exigir_login();

// Nao armazenar credenciais MySQL no repositorio ou em arquivos publicos.
function db_env(string $nome): string {
    $valor = getenv($nome);
    if ($valor === false || $valor === '') {
        http_response_code(503);
        exit('Banco de dados nao configurado.');
    }
    return $valor;
}
$porta = getenv('DB_PORT') ?: '3306';
if (!ctype_digit($porta) || (int) $porta < 1 || (int) $porta > 65535) {
    http_response_code(503);
    exit('Porta de banco invalida.');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli(
        db_env('DB_HOST'),
        db_env('DB_USER'),
        db_env('DB_PASSWORD'),
        db_env('DB_NAME'),
        (int) $porta
    );
    $mysqli->set_charset('utf8mb4');
} catch (mysqli_sql_exception $erro) {
    error_log('Erro de conexao MySQL no CRUD de clientes.');
    http_response_code(503);
    exit('Nao foi possivel conectar ao banco de dados.');
}
function formatar_data($data): string {
    if (!$data) return '';
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $data);
    return $parsed !== false ? $parsed->format('d/m/Y') : (string) $data;
}
function formatar_telefone($telefone): string {
    if (!$telefone) return '';
    $digitos = preg_replace('/\D/', '', (string) $telefone);
    if (strlen($digitos) !== 11) return (string) $telefone;
    return '(' . substr($digitos, 0, 2) . ') ' . substr($digitos, 2, 5) . '-' . substr($digitos, 7);
}

<?php
declare(strict_types=1);

/** Protecoes compartilhadas das paginas do CRUD. */
function iniciar_sessao(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
    if (!headers_sent()) {
        header('Cache-Control: no-store, private');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: same-origin');
    }
}

function h($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string {
    iniciar_sessao();
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificar_csrf(): void {
    $token = $_POST['csrf_token'] ?? null;
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Formulario invalido. Atualize a pagina.');
    }
}

function exigir_login(): void {
    iniciar_sessao();
    if (($_SESSION['administrador_autenticado'] ?? false) !== true) {
        header('Location: login.php', true, 302);
        exit;
    }
}

function credenciais_configuradas(): bool {
    return (string) getenv('APP_ADMIN_USER') !== ''
        && (string) getenv('APP_ADMIN_PASSWORD_HASH') !== '';
}

function autenticar_administrador(string $usuario, string $senha): bool {
    $usuarioEsperado = (string) getenv('APP_ADMIN_USER');
    $hash = (string) getenv('APP_ADMIN_PASSWORD_HASH');
    if ($usuarioEsperado === '' || $hash === '') {
        return false;
    }
    $senhaCorreta = password_verify($senha, $hash);
    return hash_equals($usuarioEsperado, $usuario) && $senhaCorreta;
}

function mensagem_flash(?string $mensagem = null): ?string {
    iniciar_sessao();
    if ($mensagem !== null) {
        $_SESSION['mensagem_flash'] = $mensagem;
        return null;
    }
    $atual = $_SESSION['mensagem_flash'] ?? null;
    unset($_SESSION['mensagem_flash']);
    return is_string($atual) ? $atual : null;
}

<?php
require_once __DIR__ . '/seguranca.php';
iniciar_sessao();
if (($_SESSION['administrador_autenticado'] ?? false) === true) {
    header('Location: clientes.php', true, 303);
    exit;
}
if (!credenciais_configuradas()) {
    http_response_code(503);
    exit('Login nao configurado. Defina APP_ADMIN_USER e APP_ADMIN_PASSWORD_HASH no servidor.');
}
$erro = '';
$usuario = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $usuario = is_string($usuario) ? trim($usuario) : '';
    $senha = is_string($senha) ? $senha : '';
    $bloqueadoAte = (int) ($_SESSION['bloqueado_ate'] ?? 0);
    if (time() < $bloqueadoAte) {
        $erro = 'Muitas tentativas. Tente novamente mais tarde.';
    } elseif (strlen($usuario) > 100 || strlen($senha) > 1024 || !autenticar_administrador($usuario, $senha)) {
        $_SESSION['tentativas_login'] = (int) ($_SESSION['tentativas_login'] ?? 0) + 1;
        if ($_SESSION['tentativas_login'] >= 5) {
            $_SESSION['bloqueado_ate'] = time() + 900;
            $_SESSION['tentativas_login'] = 0;
        }
        $erro = 'Usuario ou senha incorretos.';
    } else {
        session_regenerate_id(true);
        $_SESSION['administrador_autenticado'] = true;
        unset($_SESSION['tentativas_login'], $_SESSION['bloqueado_ate']);
        header('Location: clientes.php', true, 303);
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar — CRUD de Clientes</title>
  <style>
    body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f2f5fa; font-family: system-ui, sans-serif; color: #172b4d; }
    main { box-sizing: border-box; width: min(400px, calc(100% - 32px)); background: white; padding: 28px; border: 1px solid #d9e2ef; border-radius: 14px; box-shadow: 0 8px 24px #1b355012; }
    h1 { font-size: 1.6rem; margin-top: 0; }
    label { display: block; margin: 15px 0 5px; font-weight: 600; }
    input { box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #b9c5d4; border-radius: 7px; font: inherit; }
    button { width: 100%; background: #174f85; color: white; border: 0; border-radius: 7px; padding: 12px; margin-top: 20px; font: inherit; cursor: pointer; }
    .erro { color: #a32828; }
  </style>
</head>
<body>
  <main>
    <h1>Acesso administrativo</h1>
    <p>Entre para gerenciar os clientes cadastrados.</p>
    <?php if ($erro !== ''): ?><p class="erro" role="alert"><?= h($erro) ?></p><?php endif; ?>
    <form method="post" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <label for="usuario">Usuario</label>
      <input id="usuario" type="text" name="usuario" value="<?= h($usuario) ?>" maxlength="100" autocomplete="username" required>
      <label for="senha">Senha</label>
      <input id="senha" type="password" name="senha" autocomplete="current-password" required>
      <button type="submit">Entrar</button>
    </form>
  </main>
</body>
</html>

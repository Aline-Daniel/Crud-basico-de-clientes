<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/validacao.php';
$erro = '';
$valores = ['nome' => '', 'email' => '', 'telefone' => '', 'nascimento' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    foreach ($valores as $chave => $_) {
        $valores[$chave] = is_string($_POST[$chave] ?? null) ? $_POST[$chave] : '';
    }
    try {
        [$nome, $email, $telefone, $nascimento] = validar_dados_cliente($_POST);
        $stmt = $mysqli->prepare('INSERT INTO clientes (nome, email, telefone, nascimento, data) VALUES (?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssss', $nome, $email, $telefone, $nascimento);
        $stmt->execute();
        $stmt->close();
        mensagem_flash('Cliente cadastrado com sucesso.');
        header('Location: clientes.php', true, 303);
        exit;
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (mysqli_sql_exception $e) {
        error_log('Falha ao cadastrar cliente.');
        $erro = 'Nao foi possivel salvar os dados. Verifique o banco.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastrar cliente</title><link rel="stylesheet" href="assets/estilo.css">
</head>
<body>
<?php require __DIR__ . '/cabecalho.php'; ?>
<main>
  <section class="card">
    <h1>Novo cliente</h1>
    <p class="subtitulo">Preencha os dados para adicionar um novo cadastro.</p>
    <?php if ($erro !== ''): ?><p class="erro" role="alert"><?= h($erro) ?></p><?php endif; ?>
    <form method="post" action="cadastrar_cliente.php">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <label for="nome">Nome *</label>
      <input id="nome" name="nome" type="text" maxlength="100" required value="<?= h($valores['nome']) ?>">
      <label for="email">E-mail *</label>
      <input id="email" name="email" type="email" maxlength="100" required value="<?= h($valores['email']) ?>">
      <label for="telefone">Celular com DDD (opcional)</label>
      <input id="telefone" name="telefone" type="tel" maxlength="25" placeholder="(11) 98888-8888" value="<?= h($valores['telefone']) ?>">
      <label for="nascimento">Nascimento (opcional)</label>
      <input id="nascimento" name="nascimento" type="text" maxlength="10" placeholder="DD/MM/AAAA" value="<?= h($valores['nascimento']) ?>">
      <div class="acoes">
        <button type="submit">Salvar cliente</button>
        <a class="botao botao-secundario" href="clientes.php">Cancelar</a>
      </div>
    </form>
  </section>
</main>
</body></html>

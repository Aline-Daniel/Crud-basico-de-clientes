<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/validacao.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('ID de cliente invalido.');
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    try {
        [$nome, $email, $telefone, $nascimento] = validar_dados_cliente($_POST);
        $stmt = $mysqli->prepare('UPDATE clientes SET nome = ?, email = ?, telefone = ?, nascimento = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $nome, $email, $telefone, $nascimento, $id);
        $stmt->execute();
        $atualizados = $stmt->affected_rows;
        $stmt->close();
        if ($atualizados === 0) {
            // Sem alteracoes e uma atualizacao com ID inexistente precisam ser diferenciadas abaixo.
            $stmt = $mysqli->prepare('SELECT id FROM clientes WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $existe = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            if (!$existe) {
                http_response_code(404);
                exit('Cliente nao encontrado.');
            }
        }
        mensagem_flash('Cliente atualizado com sucesso.');
        header('Location: clientes.php', true, 303);
        exit;
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (mysqli_sql_exception $e) {
        error_log('Falha ao atualizar cliente.');
        $erro = 'Nao foi possivel atualizar os dados.';
    }
}
$stmt = $mysqli->prepare('SELECT nome, email, telefone, nascimento FROM clientes WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$cliente) {
    http_response_code(404);
    exit('Cliente nao encontrado.');
}
$valores = [
    'nome' => $cliente['nome'],
    'email' => $cliente['email'],
    'telefone' => $cliente['telefone'] ? formatar_telefone($cliente['telefone']) : '',
    'nascimento' => $cliente['nascimento'] ? formatar_data($cliente['nascimento']) : '',
];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $erro !== '') {
    foreach ($valores as $chave => $_) {
        $valores[$chave] = is_string($_POST[$chave] ?? null) ? $_POST[$chave] : '';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar cliente</title><link rel="stylesheet" href="assets/estilo.css">
</head>
<body>
<?php require __DIR__ . '/cabecalho.php'; ?>
<main>
  <section class="card">
    <h1>Editar cliente #<?= (int) $id ?></h1>
    <p class="subtitulo">Atualize apenas as informacoes necessarias.</p>
    <?php if ($erro !== ''): ?><p class="erro" role="alert"><?= h($erro) ?></p><?php endif; ?>
    <form method="post" action="editar_cliente.php?id=<?= (int) $id ?>">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <label for="nome">Nome *</label>
      <input id="nome" type="text" name="nome" maxlength="100" required value="<?= h($valores['nome']) ?>">
      <label for="email">E-mail *</label>
      <input id="email" type="email" name="email" maxlength="100" required value="<?= h($valores['email']) ?>">
      <label for="telefone">Celular com DDD (opcional)</label>
      <input id="telefone" type="tel" name="telefone" maxlength="25" value="<?= h($valores['telefone']) ?>">
      <label for="nascimento">Nascimento (opcional)</label>
      <input id="nascimento" type="text" name="nascimento" maxlength="10" placeholder="DD/MM/AAAA" value="<?= h($valores['nascimento']) ?>">
      <div class="acoes">
        <button type="submit">Salvar alteracoes</button>
        <a class="botao botao-secundario" href="clientes.php">Cancelar</a>
      </div>
    </form>
  </section>
</main>
</body></html>

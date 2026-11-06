<?php
require_once __DIR__ . '/conexao.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('ID de cliente invalido.');
}
$stmt = $mysqli->prepare('SELECT nome FROM clientes WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$cliente) {
    http_response_code(404);
    exit('Cliente nao encontrado.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    if (($_POST['confirmar'] ?? null) !== '1') {
        http_response_code(400);
        exit('Confirmacao invalida.');
    }
    try {
        $stmt = $mysqli->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        mensagem_flash('Cliente excluido com sucesso.');
        header('Location: clientes.php', true, 303);
        exit;
    } catch (mysqli_sql_exception $e) {
        error_log('Falha ao excluir cliente.');
        http_response_code(503);
        exit('Nao foi possivel excluir o cliente.');
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar exclusao</title><link rel="stylesheet" href="assets/estilo.css">
</head>
<body>
<?php require __DIR__ . '/cabecalho.php'; ?>
<main>
  <section class="card">
    <h1>Excluir cliente?</h1>
    <p>Tem certeza que deseja excluir o cadastro de <strong><?= h($cliente['nome']) ?></strong>?</p>
    <p class="erro">Esta acao nao pode ser desfeita.</p>
    <form action="deletar_cliente.php?id=<?= (int) $id ?>" method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="acoes">
        <button class="botao-perigo" name="confirmar" value="1" type="submit">Sim, excluir cliente</button>
        <a class="botao botao-secundario" href="clientes.php">Cancelar</a>
      </div>
    </form>
  </section>
</main>
</body></html>

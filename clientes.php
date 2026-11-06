<?php
require_once __DIR__ . '/conexao.php';
try {
    $query = $mysqli->query('SELECT id, nome, email, telefone, nascimento, data FROM clientes ORDER BY id DESC');
    $clientes = $query->fetch_all(MYSQLI_ASSOC);
    $query->free();
} catch (mysqli_sql_exception $e) {
    error_log('Falha ao consultar clientes.');
    http_response_code(503);
    exit('Nao foi possivel consultar clientes.');
}
$mensagem = mensagem_flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clientes cadastrados</title><link rel="stylesheet" href="assets/estilo.css">
</head>
<body>
<?php require __DIR__ . '/cabecalho.php'; ?>
<main>
  <section class="card">
    <h1>Clientes cadastrados</h1>
    <p class="subtitulo">Gerencie os dados do cadastro com acesso restrito.</p>
    <?php if ($mensagem): ?><p class="aviso" role="status"><?= h($mensagem) ?></p><?php endif; ?>
    <p><a class="botao" href="cadastrar_cliente.php">+ Cadastrar cliente</a></p>
    <div class="tabela-rolagem">
      <table>
        <thead><tr>
          <th scope="col">ID</th><th scope="col">Nome</th><th scope="col">E-mail</th>
          <th scope="col">Telefone</th><th scope="col">Nascimento</th>
          <th scope="col">Cadastro</th><th scope="col">Acoes</th>
        </tr></thead>
        <tbody>
        <?php if (!$clientes): ?>
          <tr><td colspan="7">Nenhum cliente cadastrado.</td></tr>
        <?php else: foreach ($clientes as $cliente): ?>
          <tr>
            <td><?= h($cliente['id']) ?></td>
            <td><?= h($cliente['nome']) ?></td>
            <td><?= h($cliente['email']) ?></td>
            <td><?= h(formatar_telefone($cliente['telefone'])) ?></td>
            <td><?= h(formatar_data($cliente['nascimento'])) ?></td>
            <td><?= h(date('d/m/Y H:i', strtotime($cliente['data']))) ?></td>
            <td>
              <a href="editar_cliente.php?id=<?= (int) $cliente['id'] ?>">Editar</a>
              <a href="deletar_cliente.php?id=<?= (int) $cliente['id'] ?>">Excluir</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
    <p><small><?= count($clientes) ?> cliente(s) encontrado(s).</small></p>
  </section>
</main>
</body></html>

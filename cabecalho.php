<?php
require_once __DIR__ . '/seguranca.php';
exigir_login();
?>
<header class="topo">
  <div class="topo-conteudo">
    <a class="marca" href="clientes.php">Gestao de clientes</a>
    <nav aria-label="Navegacao principal">
      <a href="clientes.php">Clientes</a>
      <a href="cadastrar_cliente.php">Novo cadastro</a>
      <form action="logout.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <button class="sair" type="submit">Sair</button>
      </form>
    </nav>
  </div>
</header>

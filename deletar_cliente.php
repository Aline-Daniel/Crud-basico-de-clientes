<?php
require_once('conexao.php');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { http_response_code(400); exit('ID inválido'); }
if(isset($_POST['confirmar'])) {
    verificar_csrf();

    $stmt = $mysqli->prepare('DELETE FROM clientes WHERE id = ?');
    $stmt->bind_param('i', $id);
    try { $sql_query = $stmt->execute(); }
    catch (mysqli_sql_exception $e) {
        error_log('Erro ao excluir cliente');
        http_response_code(503);
        exit('Não foi possível excluir o cliente.');
    }
    $stmt->close();

    if($sql_query) { ?>
        <h1>Cliente deletado com sucesso!</h1>
        <p><a href="clientes.php">Clique aqui</a> para voltar para a lista de clientes.</p>
        <?php
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar Cliente</title>
</head>
<body>
    <h1>Tem certeza que deseja deletar este cliente?</h1>
    
    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
        <a style="margin-right:40px;" href="clientes.php">Não</a>
        <button name="confirmar" value="1" type="submit">Sim</button>
    </form>
</body>
</html>
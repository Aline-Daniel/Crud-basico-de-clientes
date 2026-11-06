<?php

include('conexao.php');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { http_response_code(400); exit('ID inválido'); }
function limpar_texto($str){ 
    return preg_replace("/[^0-9]/", "", $str); 
}

if(count($_POST) > 0) {
    verificar_csrf();

    $erro = false;
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $nascimento = $_POST['nascimento'];

    if(empty($nome)) {
        $erro = "Preencha o nome";
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Preencha o e-mail";
    }

    if(!empty($nascimento)) { 
        $pedacos = explode('/', $nascimento);
        if(count($pedacos) == 3) {
            $nascimento = implode ('-', array_reverse($pedacos));
        } else {
            $erro = "A data de nascimento deve seguir o padrão dia/mes/ano.";
        }
    }

    if(!empty($telefone)) {
        $telefone = limpar_texto($telefone);
        if(strlen($telefone) != 11)
            $erro = "O telefone deve ser preenchido no padrão (11) 98888-8888";
    }

    if($erro) {
        echo "<p><b>ERRO: $erro</b></p>";
    } else {
        $stmt = $mysqli->prepare('UPDATE clientes SET nome = ?, email = ?, telefone = ?, nascimento = ? WHERE id = ?');
        $nascimento = $nascimento ?: null;
        $stmt->bind_param('ssssi', $nome, $email, $telefone, $nascimento, $id);
        try { $deu_certo = $stmt->execute(); }
        catch (mysqli_sql_exception $e) {
            error_log('Erro ao atualizar cliente');
            $deu_certo = false;
            echo '<p>Não foi possível atualizar o cliente.</p>';
        }
        $stmt->close();
        if($deu_certo) {
            echo "<p><b>Cliente atualizado com sucesso!!!</b></p>";
            unset($_POST);
        }
    }

}

$stmt = $mysqli->prepare('SELECT * FROM clientes WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$query_cliente = $stmt->get_result();
$cliente = $query_cliente->fetch_assoc();
$stmt->close();
if (!$cliente) { http_response_code(404); exit('Cliente não encontrado'); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
</head>
<body>
    <a href="clientes.php">Voltar para a lista</a>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
        <p>
            <label>Nome:</label>
            <input value="<?php echo h($cliente['nome']); ?>" name="nome" type="text">
        </p>
        <p>
            <label>E-mail:</label>
            <input value="<?php echo h($cliente['email']); ?>" name="email" type="text">
        </p>
        <p>
            <label>Telefone:</label>
            <input value="<?php if(!empty($cliente['telefone'])) echo h(formatar_telefone($cliente['telefone'])); ?>"  placeholder="(11) 98888-8888" name="telefone" type="text">
        </p>
        <p>
            <label>Data de Nascimento:</label>
            <input value="<?php if(!empty($cliente['nascimento'])) echo h(formatar_data($cliente['nascimento'])); ?>"  name="nascimento" type="text">
        </p>
        <p>
            <button type="submit">Salvar Cliente</button>
        </p>
    </form>
</body>
</html>
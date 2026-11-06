<?php
require_once __DIR__ . '/../validacao.php';
require_once __DIR__ . '/../seguranca.php';

function teste(bool $condicao, string $descricao): void {
    if (!$condicao) {
        fwrite(STDERR, "FALHOU: $descricao\n");
        exit(1);
    }
    echo "OK: $descricao\n";
}
function falha_esperada(array $dados, string $nome): void {
    try {
        validar_dados_cliente($dados);
    } catch (InvalidArgumentException $e) {
        teste(true, $nome);
        return;
    }
    teste(false, $nome);
}
$dados = validar_dados_cliente([
    'nome' => 'Pessoa de Teste',
    'email' => 'teste@example.invalid',
    'telefone' => '(11) 98888-1234',
    'nascimento' => '29/02/2024'
]);
teste($dados === ['Pessoa de Teste', 'teste@example.invalid', '11988881234', '2024-02-29'], 'Normalizacao do cadastro');
$semOpcionais = validar_dados_cliente(['nome' => 'Teste', 'email' => 'teste@example.invalid']);
teste($semOpcionais[2] === null && $semOpcionais[3] === null, 'Campos opcionais nulos');
falha_esperada(['nome' => '<script>', 'email' => 'invalido'], 'Email invalido');
falha_esperada(['nome' => 'Teste', 'email' => 'teste@example.invalid', 'nascimento' => '31/02/2024'], 'Data impossivel');
falha_esperada(['nome' => 'Teste', 'email' => 'teste@example.invalid', 'telefone' => '12345'], 'Telefone invalido');
falha_esperada(['nome' => ['array'], 'email' => 'teste@example.invalid'], 'Rejeita array em formulario');
teste(h('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;', 'Escape HTML');
putenv('APP_ADMIN_USER=admin_teste');
putenv('APP_ADMIN_PASSWORD_HASH=' . password_hash('senha_temporaria_de_teste', PASSWORD_DEFAULT));
teste(autenticar_administrador('admin_teste', 'senha_temporaria_de_teste'), 'Senha valida via hash');
teste(!autenticar_administrador('admin_teste', 'outra'), 'Senha invalida');
teste(!autenticar_administrador('outro', 'senha_temporaria_de_teste'), 'Usuario invalido');
echo "Todos os testes basicos passaram.\n";

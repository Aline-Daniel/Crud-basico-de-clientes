<?php
declare(strict_types=1);

/** Retorna os valores normalizados para consultas preparadas. */
function validar_dados_cliente(array $campos): array {
    foreach (['nome', 'email', 'telefone', 'nascimento'] as $chave) {
        if (isset($campos[$chave]) && !is_string($campos[$chave])) {
            throw new InvalidArgumentException('Dados do formulario invalidos.');
        }
    }
    $nome = trim($campos['nome'] ?? '');
    $email = trim($campos['email'] ?? '');
    $telefone = trim($campos['telefone'] ?? '');
    $nascimento = trim($campos['nascimento'] ?? '');

    if ($nome === '' || mb_strlen($nome, 'UTF-8') > 100) {
        throw new InvalidArgumentException('Informe um nome de ate 100 caracteres.');
    }
    if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Informe um e-mail valido de ate 100 caracteres.');
    }
    if ($telefone !== '') {
        $telefone = preg_replace('/\D/', '', $telefone);
        if (strlen($telefone) !== 11) {
            throw new InvalidArgumentException('Informe um celular com DDD e 11 digitos.');
        }
    }
    if ($nascimento !== '') {
        $data = DateTimeImmutable::createFromFormat('!d/m/Y', $nascimento);
        if ($data === false || $data->format('d/m/Y') !== $nascimento) {
            throw new InvalidArgumentException('Data invalida. Use DD/MM/AAAA.');
        }
        $nascimento = $data->format('Y-m-d');
    }
    return [$nome, $email, $telefone === '' ? null : $telefone, $nascimento === '' ? null : $nascimento];
}

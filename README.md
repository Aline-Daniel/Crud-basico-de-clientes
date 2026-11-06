# CRUD de Clientes — PHP + MySQL

Projeto didático de cadastro, listagem, edição e exclusão de clientes com PHP, MySQL e formulários HTML.

## Recursos
- Banco de dados relacional MySQL e operações CRUD.
- Uso de consultas preparadas para inserção, consulta, atualização e exclusão.
- Escapamento dos valores inseridos no HTML e proteção CSRF nos formulários.

## Executar localmente
Requisitos: PHP 8.1+ com extensão mysqli e MySQL. Crie um banco vazio exclusivo para testes e um usuário com permissões adequadas.
Revise antes de importar o arquivo crud_clientes.sql: ELE CONTÉM DROP TABLE e dados demonstrativos; pode apagar registros existentes.
Configure as variáveis de ambiente DB_HOST, DB_PORT (padrão 3306), DB_NAME, DB_USER e DB_PASSWORD no processo PHP. O arquivo .env.example NÃO é carregado automaticamente.
Depois execute: php -S 127.0.0.1:8000
Abra: http://127.0.0.1:8000/clientes.php

## Arquivos principais
- conexao.php: MySQL, configuração do ambiente, escapamento e proteção CSRF.
- cadastrar_cliente.php: formulário de cadastro.
- clientes.php: lista de clientes.
- editar_cliente.php: alteração com confirmação.
- deletar_cliente.php: exclusão confirmada por formulário.
- crud_clientes.sql: estrutura e dados de demonstração.

## Segurança
IMPORTANTE: versões antigas do projeto publicavam credenciais no código. Remover a senha atual NÃO remove o histórico Git. Se essas credenciais forem reais ou reutilizadas, revogue-as ou troque-as imediatamente no banco MySQL.
O projeto não dispõe de login nem autorização por usuário. Por isso, NÃO é seguro publicá-lo na internet com clientes ou dados pessoais reais. Antes de utilizar em produção, implemente autenticação, autorização e HTTPS, além de testar os formulários em ambiente isolado.

## Validação
O GitHub Actions verifica a sintaxe dos scripts PHP; ainda não foram feitos testes completos de integração com MySQL.

Origem: repositório público anterior AlineDaniel/Crud-basico-de-clientes.

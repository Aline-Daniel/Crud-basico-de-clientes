# CRUD de Clientes — PHP + MySQL

Projeto demonstrativo de cadastro, listagem, edicao e exclusao de clientes com **PHP 8.2+**, **MySQL**, login administrativo e interface responsiva.

> **Projeto de portfolio e aprendizado.** Nao hospede publicamente com clientes reais sem avaliacoes adicionais de seguranca, HTTPS, politicas de acesso e adequacao a LGPD.

## Funcionalidades
- Login de um administrador, autenticado com senha armazenada como hash fora do codigo.
- Cadastro, listagem, edicao e exclusao de clientes.
- Validacao de e-mail, celular e datas; consultas preparadas no MySQL.
- Protecao CSRF nas operacoes POST e escapamento de saida HTML.
- Sessao HTTP-only, renovacao da sessao apos login, logout por POST, controles basicos contra tentativas repetidas e cabecalhos de seguranca.
- Layout responsivo para desktop e celular.
- Testes automatizados no GitHub Actions.

## Rodar no Windows, somente no computador

**Pre-requisitos:** PHP 8.2+ com extensoes mysqli e mbstring, MySQL e um banco de testes isolado.

1. Crie um banco MySQL vazio chamado **crud_clientes_teste** e um usuario com acesso limitado a esse banco. Importe **crud_clientes.sql**. O script nao apaga tabelas nem insere dados reais.
2. Abra um terminal PowerShell na pasta do projeto.
3. Gere um hash de senha sem colocar a senha no codigo (digite uma senha forte e pressione Enter):

   ~~~powershell
   php -r 'echo password_hash(trim(fgets(STDIN)), PASSWORD_DEFAULT), PHP_EOL;'
   ~~~

4. No **mesmo terminal**, defina as variaveis de ambiente (substitua os valores ilustrativos pelos locais):

   ~~~powershell
   $env:DB_HOST = "127.0.0.1"
   $env:DB_PORT = "3306"
   $env:DB_NAME = "crud_clientes_teste"
   $env:DB_USER = "usuario_teste"
   $env:DB_PASSWORD = "SENHA_LOCAL_DO_BANCO"
   $env:APP_ADMIN_USER = "administrador"
   $env:APP_ADMIN_PASSWORD_HASH = "COLE_AQUI_O_HASH_GERADO"
   ~~~

5. Inicie o servidor web local:

   ~~~powershell
   php -S 127.0.0.1:8000
   ~~~

6. Abra http://127.0.0.1:8000/login.php e entre com o usuario e a senha configurados.

**Atencao:** nao adicione arquivos .env, senhas de banco, hashes administrativos reais ou dados de clientes ao GitHub. O arquivo .env.example demonstra os nomes das variaveis e nao e carregado automaticamente.

## Testes

~~~powershell
php tests/testar_validacao.php
php -l login.php
~~~

O workflow no GitHub Actions verifica a sintaxe dos PHP, testes de validacao/autenticacao e a integracao HTTP com um banco MySQL **temporario**, usando credenciais somente para testes.

## Estrutura
- **login.php**, **logout.php**, **seguranca.php**: autenticacao, sessao e CSRF.
- **conexao.php**: conexao MySQL por variaveis de ambiente; exige login.
- **validacao.php**: regras dos campos cadastrados.
- **clientes.php**, **cadastrar_cliente.php**, **editar_cliente.php**, **deletar_cliente.php**: CRUD.
- **cabecalho.php**, **assets/estilo.css**: interface.
- **crud_clientes.sql**: estrutura segura para novas instalacoes em banco vazio.
- **tests/**: testes automatizados.

## Limitacoes e seguranca

- Este exemplo suporta **um administrador**, sem gestao de usuarios, perfis, recuperacao de senha ou auditoria. A limitacao de tentativas de login funciona por sessao; para exposicao publica e necessario acrescentar controle no servidor/proxy, monitoramento e MFA.
- A aplicacao deve ser hospedada somente com HTTPS e configuracao de sessao/cookies apropriada. A execucao com o servidor PHP embutido e destinada a desenvolvimento local.
- **O repositorio original incluiu credenciais MySQL em um commit publico.** Remover a senha do codigo atual NAO remove o historico de commits ou o repositorio de origem. **Revogue ou troque essas credenciais no MySQL**, caso ainda existam. O historico publico deve ser revisado antes de qualquer uso real.
- A atualizacao muda o script SQL do projeto para evitar DROP TABLE. Em um banco existente, CREATE TABLE IF NOT EXISTS nao altera automaticamente tabelas antigas com MyISAM ou charset antigo; faca backup e planeje migracao separadamente.
- Dados reais de clientes sao pessoais. Considere minimizacao de dados, controles de acesso e obrigacoes da LGPD antes de uso fora de uma demonstracao.

Origem: fork do repositorio anterior **AlineDaniel/Crud-basico-de-clientes**.

-- Schema de demonstracao seguro para um banco de TESTES vazio.
-- Nao contem DROP TABLE nem dados pessoais.
-- CREATE TABLE IF NOT EXISTS nao substitui tabelas existentes.
CREATE TABLE IF NOT EXISTS clientes (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(11) NULL,
    nascimento DATE NULL,
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criação da Base de Dados
CREATE DATABASE borboleta_azul;
USE borboleta_azul;

-- Tabela de Utilizadores (Membros e Admins)
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso ENUM('membro', 'admin') DEFAULT 'membro',
    qrcode_path VARCHAR(255),
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Eventos
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    data_evento DATETIME NOT NULL,
    vagas_totais INT NOT NULL,
    vagas_ocupadas INT DEFAULT 0
);

-- Tabela de Inscrições (Relação entre Utilizador e Evento)
CREATE TABLE inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilizador INT,
    id_evento INT,
    data_inscricao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilizador) REFERENCES utilizadores(id),
    FOREIGN KEY (id_evento) REFERENCES eventos(id)
);
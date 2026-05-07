-- ==========================================================
-- ESTRUTURA DA BASE DE DADOS: BORBOLETA AZUL
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `borboleta_azul` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `borboleta_azul`;

-- --------------------------------------------------------
-- 1. TABELA: utilizadores
-- Armazena membros e administradores
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` enum('membro','admin') DEFAULT 'membro',
  `qrcode_path` varchar(255) DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. TABELA: eventos
-- Gere as atividades e o controlo de vagas
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descricao` text,
  `data_evento` datetime NOT NULL,
  `vagas_totais` int NOT NULL,
  `vagas_ocupadas` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. TABELA: inscricoes
-- Relação entre utilizadores e eventos
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `inscricoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilizador` int DEFAULT NULL,
  `id_evento` int DEFAULT NULL,
  `data_inscricao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_utilizador` (`id_utilizador`),
  KEY `id_evento` (`id_evento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. TABELA: mensagens_contactos
-- Registo de contactos feitos através do site
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `mensagens_contactos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(150) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- INSERÇÃO DE DADOS DE TESTE (UTILIZADORES)
-- Password padrão para ambos: 12345678aA!
-- ==========================================================

INSERT INTO `utilizadores` (`nome`, `email`, `data_nascimento`, `senha`, `nivel_acesso`, `qrcode_path`) VALUES
(
  'Administrador', 
  'admin@borboleta.pt', 
  '1990-01-01', 
  '$2y$10$psxMFCqE1zau7khmpBi6eOhhmQi.C/tjpkOPRuZHgHlPVBNUXfLRK', 
  'admin', 
  'qrcodes/qr_admin.png'
),
(
  'João Membro', 
  'membro@borboleta.pt', 
  '2000-05-15', 
  '$2y$10$psxMFCqE1zau7khmpBi6eOhhmQi.C/tjpkOPRuZHgHlPVBNUXfLRK', 
  'membro', 
  'qrcodes/qr_membro.png'
);

COMMIT;

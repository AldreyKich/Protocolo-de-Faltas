-- =============================================
-- CRIACAO DO BANCO E TABELAS
-- Sistema: Protocolo Eletronico de Justificativas de Faltas
-- =============================================

CREATE DATABASE IF NOT EXISTS protocolo_faltas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE protocolo_faltas;

CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  perfil ENUM('ADMINISTRADOR', 'SECRETARIA', 'VISUALIZADOR') NOT NULL DEFAULT 'VISUALIZADOR',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS responsavel (
  id_responsavel INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  cpf CHAR(11) NOT NULL UNIQUE,
  telefone VARCHAR(20) NULL,
  email VARCHAR(150) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aluno (
  id_aluno INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  matricula VARCHAR(30) NOT NULL UNIQUE,
  turma VARCHAR(20) NOT NULL,
  email VARCHAR(150) NULL,
  telefone VARCHAR(20) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS protocolo (
  id_protocolo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero_protocolo VARCHAR(32) NOT NULL UNIQUE,
  data_falta DATE NOT NULL,
  motivo TEXT NOT NULL,
  status ENUM('ENVIADO', 'EM_ANALISE', 'APROVADO', 'REJEITADO') NOT NULL DEFAULT 'ENVIADO',
  observacoes TEXT NULL,
  data_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_aluno INT UNSIGNED NOT NULL,
  id_responsavel INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NULL,
  INDEX idx_protocolo_status (status),
  INDEX idx_protocolo_data_falta (data_falta),
  INDEX idx_protocolo_data_envio (data_envio),
  CONSTRAINT fk_protocolo_aluno
    FOREIGN KEY (id_aluno) REFERENCES aluno(id_aluno)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_protocolo_responsavel
    FOREIGN KEY (id_responsavel) REFERENCES responsavel(id_responsavel)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_protocolo_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS anexo (
  id_anexo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome_arquivo VARCHAR(255) NOT NULL,
  caminho_arquivo VARCHAR(255) NOT NULL,
  tipo_arquivo VARCHAR(120) NULL,
  id_protocolo INT UNSIGNED NOT NULL,
  data_upload TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_anexo_protocolo (id_protocolo),
  CONSTRAINT fk_anexo_protocolo
    FOREIGN KEY (id_protocolo) REFERENCES protocolo(id_protocolo)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

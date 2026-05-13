-- =============================================
-- SEED: Dados iniciais para o sistema
-- Execute após o SQL_Cria_BD_Tabelas.sql
-- =============================================

USE protocolo_faltas;

-- Usuário administrador padrão
-- Senha: password (altere após o primeiro acesso!)
INSERT INTO usuario (nome, email, senha, perfil, ativo)
VALUES (
    'Administrador',
    'admin@escola.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: password
    'ADMINISTRADOR',
    1
);

-- Usuário secretaria
INSERT INTO usuario (nome, email, senha, perfil, ativo)
VALUES (
    'Secretaria',
    'secretaria@escola.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: password
    'SECRETARIA',
    1
);

-- Alunos de exemplo
INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('FELIPE DAVI VAN DER VEEN DOS SANTOS', '2026001', '3A', 'felipe@email.com', '(44) 99999-0001', 1, '2026-05-06 11:51:51', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('BRENO FELIPE PAZ COSMA', '2026002', '3A', 'breno@email.com', '(44) 99999-0002', 1, '2026-05-06 11:51:51', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('MURILO ANTONIO DE FREITAS BISAO', '2026003', '2B', 'murilo@email.com', '(44) 99999-0003', 1, '2026-05-06 11:51:51', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('MICHAUD BENA', '2026004', '2B', 'michaud@email.com', '(44) 99999-0004', 1, '2026-05-06 11:51:51', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('JULIO CEZAR WANDRASKI', '2026005', '1C', 'julio@email.com', '(44) 99999-0005', 1, '2026-05-06 11:51:51', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('SINEIDE DA COSTA TEIXEIRA WANDRASKI', '2026006', '3A', 'sineide@email.com', '(44) 99999-0006', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('ALLAN DA SILVA GEHLEN', '2026007', '3A', 'allan@email.com', '(44) 99999-0007', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('RICARDO MARCHI', '2026008', '3A', 'ricardo@email.com', '(44) 99999-0008', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('FABIO TESSARI IRALA', '2026009', '3A', 'fabio@email.com', '(44) 99999-0009', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('ENTHONY TORRES DE OLIVEIRA', '2026010', '3A', 'enthony@email.com', '(44) 99999-0010', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');

INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo, created_at, updated_at)
VALUES ('ALDREY KICH', '2026011', '3A', 'aldrey@email.com', '(44) 99999-0011', 1, '2026-05-12 15:00:00', '2026-05-12 15:00:00');



CREATE DATABASE IF NOT EXISTS sistemaVet
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE sistemaVet;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('cliente', 'administrador')
        NOT NULL DEFAULT 'cliente',
    telefone VARCHAR(20),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_categoria INT NOT NULL,
    imagem VARCHAR(250),
    valor DECIMAL(10,2) NOT NULL,
    descricao TEXT NOT NULL,

    CONSTRAINT fk_servicos_categorias
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE horarios_disponiveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    servico_id INT NOT NULL,
    data_marcada DATE NOT NULL,
    horario TIME NOT NULL,
    disponivel TINYINT(1) NOT NULL DEFAULT 1,

    UNIQUE KEY uk_horario
        (servico_id, data_marcada, horario),

    CONSTRAINT fk_horarios_servicos
        FOREIGN KEY (servico_id)
        REFERENCES servicos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    servico_id INT NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    status_solicitacao
        ENUM('pendente', 'confirmada', 'cancelada')
        NOT NULL DEFAULT 'pendente',
    quantidade INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    data_solicitacao DATETIME
        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_solicitacoes_usuarios
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id),

    CONSTRAINT fk_solicitacoes_servicos
        FOREIGN KEY (servico_id)
        REFERENCES servicos(id)
) ENGINE=InnoDB;

CREATE TABLE animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitacao_id INT NOT NULL,
    horario_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(30) NOT NULL,
    sexo ENUM('macho', 'femea') NOT NULL,
    idade INT NOT NULL,

    CONSTRAINT fk_animais_solicitacoes
        FOREIGN KEY (solicitacao_id)
        REFERENCES solicitacoes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_animais_horarios
        FOREIGN KEY (horario_id)
        REFERENCES horarios_disponiveis(id)
) ENGINE=InnoDB;


INSERT INTO categorias (nome) VALUES
('Consultas'),
('Vacinação'),
('Procedimentos'),
('Higiene'),
('Exames');


INSERT INTO servicos
(nome, id_categoria, imagem, valor, descricao)
VALUES
('Consulta de Rotina', 1, 'cardConsultaRotina.png', 120.00,
'Avaliação geral da saúde do animal com exame físico completo e orientações preventivas.'),

('Consulta de retorno', 1, 'cardConsultaRetorno.png', 100.00,
'Consulta para acompanhamento de tratamento ou reavaliação do quadro clínico.'),

('Raio-x', 5, 'cardRaioX.png', 250.00,
'Exame de imagem utilizado para diagnosticar fraturas, lesões e alterações internas.'),

('Exame de sangue', 5, 'cardExameSangue.png', 85.50,
'Coleta e análise de sangue para avaliação da saúde geral e identificação de doenças.'),

('Castração', 3, 'cardCastracao.png', 1500.00,
'Procedimento cirúrgico que impede a reprodução, contribuindo para a saúde e controle populacional.'),

('Cirurgia', 3, 'cardCirurgia.png', 2000.00,
'Procedimentos cirúrgicos realizados com equipe especializada, garantindo segurança e recuperação adequada do animal.'),

('Escovação dentária', 4, 'cardEscovacao.png', 70.00,
'Limpeza dos dentes do animal para prevenir tártaro, mau hálito e doenças bucais.'),

('Higienização Completa', 4, 'cardHigiene.png', 950.00,
'Serviço completo com banho, tosa, limpeza de orelhas e cuidados com a pelagem.'),

('Vacina anti-rábica', 2, 'cardVacRaiva.png', 200.00,
'Vacina essencial para prevenção da raiva, protegendo a saúde do animal e da população.'),

('Vacina múltipla', 2, 'cardVacMulti.png', 500.00,
'Vacina que protege contra diversas doenças, fortalecendo o sistema imunológico do animal.');


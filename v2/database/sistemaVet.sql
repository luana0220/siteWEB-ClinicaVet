
CREATE DATABASE IF NOT EXISTS `sistemavet`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistemavet`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/11/2026 às 23:50
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistemavet`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `animais`
--

CREATE TABLE `animais` (
  `id` int(11) NOT NULL,
  `solicitacao_id` int(11) NOT NULL,
  `horario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especie` varchar(30) NOT NULL,
  `sexo` enum('macho','femea') NOT NULL,
  `idade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `animais`
--

INSERT INTO `animais` (`id`, `solicitacao_id`, `horario_id`, `nome`, `especie`, `sexo`, `idade`) VALUES
(1, 1, 1, 'Nina', 'Gato', 'femea', 5),
(2, 1, 2, 'Lua', 'Cachorro', 'femea', 1),
(3, 2, 3, 'Bidu', 'Cachorro', 'macho', 3),
(4, 3, 4, 'Luana De Melo Duarte', 'Gato', 'macho', 25),
(5, 4, 5, 'Amora', 'Cachorro', 'femea', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Consultas'),
(5, 'Exames'),
(4, 'Higiene'),
(3, 'Procedimentos'),
(2, 'Vacinação');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_disponiveis`
--

CREATE TABLE `horarios_disponiveis` (
  `id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `data_marcada` date NOT NULL,
  `horario` time NOT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horarios_disponiveis`
--

INSERT INTO `horarios_disponiveis` (`id`, `servico_id`, `data_marcada`, `horario`, `disponivel`) VALUES
(1, 1, '2026-12-10', '09:00:00', 1),
(2, 4, '2026-12-12', '10:00:00', 1),
(3, 7, '2026-12-11', '14:00:00', 0),
(4, 6, '2026-12-20', '08:00:00', 0),
(5, 5, '2026-11-23', '18:00:00', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_solicitacao`
--

CREATE TABLE `itens_solicitacao` (
  `id` int(11) NOT NULL,
  `solicitacao_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `horario_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status_item` enum('pendente','confirmada','cancelada') NOT NULL DEFAULT 'pendente',
  `cancelado_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_solicitacao`
--

INSERT INTO `itens_solicitacao` (`id`, `solicitacao_id`, `animal_id`, `servico_id`, `horario_id`, `valor`, `status_item`, `cancelado_em`) VALUES
(1, 1, 1, 1, 1, 120.00, 'cancelada', '2026-11-22 23:24:02'),
(2, 1, 2, 4, 2, 85.50, 'cancelada', '2026-11-22 23:24:02'),
(3, 2, 3, 7, 3, 70.00, 'confirmada', NULL),
(4, 3, 4, 6, 4, 2000.00, 'confirmada', NULL),
(5, 4, 5, 5, 5, 1500.00, 'confirmada', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `imagem` varchar(250) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`, `id_categoria`, `imagem`, `valor`, `descricao`) VALUES
(1, 'Consulta de Rotina', 1, 'cardConsultaRotina.png', 120.00, 'Avaliação geral da saúde do animal com exame físico completo e orientações preventivas.'),
(3, 'Raio-x', 5, 'cardRaioX.png', 300.00, 'Exame de imagem utilizado para diagnosticar fraturas, lesões e alterações internas.'),
(4, 'Exame de sangue', 5, 'cardExameSangue.png', 85.50, 'Coleta e análise de sangue para avaliação da saúde geral e identificação de doenças.'),
(5, 'Castração', 3, 'cardCastracao.png', 1500.00, 'Procedimento cirúrgico que impede a reprodução, contribuindo para a saúde e controle populacional.'),
(6, 'Cirurgia', 3, 'cardCirurgia.png', 2000.00, 'Procedimentos cirúrgicos realizados com equipe especializada, garantindo segurança e recuperação adequada do animal.'),
(7, 'Escovação dentária', 4, 'cardEscovacao.png', 70.00, 'Limpeza dos dentes do animal para prevenir tártaro, mau hálito e doenças bucais.'),
(8, 'Higienização Completa', 4, 'cardHigiene.png', 950.00, 'Serviço completo com banho, tosa, limpeza de orelhas e cuidados com a pelagem.'),
(9, 'Vacina anti-rábica', 2, 'cardVacRaiva.png', 200.00, 'Vacina essencial para prevenção da raiva, protegendo a saúde do animal e da população.'),
(10, 'Vacina múltipla', 2, 'cardVacMulti.png', 500.00, 'Vacina que protege contra diversas doenças, fortalecendo o sistema imunológico do animal.'),
(11, 'Consulta de Retorno', 1, 'cardConsultaRetorno.png', 500.00, 'Uma consulta de acompanhamento pós procedimentos cirúrgicos.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes`
--

CREATE TABLE `solicitacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `servico_id` int(11) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `status_solicitacao` enum('pendente','confirmada','cancelada') NOT NULL DEFAULT 'pendente',
  `quantidade` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `data_solicitacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `valor` decimal(10,2) DEFAULT NULL,
  `animal` varchar(100) DEFAULT NULL,
  `horario` varchar(5) DEFAULT NULL,
  `cancelado_em` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `solicitacoes`
--

INSERT INTO `solicitacoes` (`id`, `usuario_id`, `servico_id`, `telefone`, `status_solicitacao`, `quantidade`, `valor_total`, `data_solicitacao`, `valor`, `animal`, `horario`, `cancelado_em`) VALUES
(1, 2, 1, '(31) 99999-1111', 'cancelada', 2, 205.50, '2026-11-22', NULL, NULL, NULL, NULL),
(2, 3, 7, '(31) 99999-2222', 'confirmada', 1, 70.00, '2026-11-22', NULL, NULL, NULL, NULL),
(3, 2, 6, '31972633124', 'confirmada', 1, 2000.00, NULL, NULL, NULL, NULL, NULL),
(4, 2, 5, '31972633124', 'confirmada', 1, 1500.00, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('cliente','administrador') NOT NULL DEFAULT 'cliente',
  `telefone` varchar(20) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `telefone`, `data_cadastro`) VALUES
(1, 'Administrador Animed', 'admin@animed.com', '$2y$12$/fh5brkXXYCqy/24mAhMTeGggaWur7J43N14yHOMBqhKDdEMJg6sy', 'administrador', '(31) 3333-0000', '2026-11-22 19:11:42'),
(2, 'Ana Souza', 'ana@teste.com', '$2y$12$G3cj1n2A7JQvhBbCD//.W.pYenx0xhYctf8syZKbLpUO8PPeim4eO', 'cliente', '(31) 99999-1111', '2026-11-22 19:11:42'),
(3, 'Bruno Lima', 'bruno@teste.com', '$2y$12$EINH3BsyE/xKtoe6gYvH4uMq9r6QPWqh7zgO5LbLwFuHSnSz6xFre', 'cliente', '(31) 99999-2222', '2026-11-22 19:11:42');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `animais`
--
ALTER TABLE `animais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_animais_solicitacoes` (`solicitacao_id`),
  ADD KEY `fk_animais_horarios` (`horario_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `horarios_disponiveis`
--
ALTER TABLE `horarios_disponiveis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_horario` (`servico_id`,`data_marcada`,`horario`);

--
-- Índices de tabela `itens_solicitacao`
--
ALTER TABLE `itens_solicitacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_item_animal` (`animal_id`),
  ADD KEY `fk_item_solicitacao` (`solicitacao_id`),
  ADD KEY `fk_item_servico` (`servico_id`),
  ADD KEY `fk_item_horario` (`horario_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_servicos_categorias` (`id_categoria`);

--
-- Índices de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_solicitacoes_usuarios` (`usuario_id`),
  ADD KEY `fk_solicitacoes_servicos` (`servico_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `animais`
--
ALTER TABLE `animais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `horarios_disponiveis`
--
ALTER TABLE `horarios_disponiveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `itens_solicitacao`
--
ALTER TABLE `itens_solicitacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `animais`
--
ALTER TABLE `animais`
  ADD CONSTRAINT `fk_animais_horarios` FOREIGN KEY (`horario_id`) REFERENCES `horarios_disponiveis` (`id`),
  ADD CONSTRAINT `fk_animais_solicitacoes` FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `horarios_disponiveis`
--
ALTER TABLE `horarios_disponiveis`
  ADD CONSTRAINT `fk_horarios_servicos` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `itens_solicitacao`
--
ALTER TABLE `itens_solicitacao`
  ADD CONSTRAINT `fk_item_animal` FOREIGN KEY (`animal_id`) REFERENCES `animais` (`id`),
  ADD CONSTRAINT `fk_item_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios_disponiveis` (`id`),
  ADD CONSTRAINT `fk_item_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`),
  ADD CONSTRAINT `fk_item_solicitacao` FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacoes` (`id`);

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `fk_servicos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Restrições para tabelas `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD CONSTRAINT `fk_solicitacoes_servicos` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`),
  ADD CONSTRAINT `fk_solicitacoes_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/05/2026 às 21:40
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `brunosurfbd`
--
CREATE DATABASE IF NOT EXISTS `brunosurfbd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `brunosurfbd`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `aula_id` int(11) NOT NULL,
  `data_preferida` date NOT NULL,
  `horario` time NOT NULL,
  `num_pessoas` int(11) NOT NULL DEFAULT 1,
  `metodo_pagamento` varchar(30) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `status` enum('pendente','confirmado','cancelado') NOT NULL DEFAULT 'pendente',
  `data_agendamento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `nome_completo`, `whatsapp`, `aula_id`, `data_preferida`, `horario`, `num_pessoas`, `metodo_pagamento`, `observacoes`, `valor_total`, `status`, `data_agendamento`) VALUES
(1, 'Maria Oliveira', '11 98888-1111', 1, '2025-06-10', '08:00:00', 1, 'pix', 'Primeira vez no surf, tenho medo de ondas grandes.', 120.00, 'confirmado', '2026-05-25 16:40:02'),
(2, 'João e Ana Silva', '11 97777-2222', 1, '2025-06-10', '08:00:00', 3, 'visa', 'Somos 3 amigos, queremos aula juntos.', 90.00, 'pendente', '2026-05-25 16:40:02'),
(3, 'Carlos Mendes', '11 96666-3333', 2, '2025-06-11', '10:00:00', 1, 'elo', NULL, 180.00, 'confirmado', '2026-05-25 16:40:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas`
--

CREATE TABLE `aulas` (
  `id` int(11) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `max_alunos` int(11) NOT NULL DEFAULT 5,
  `preco_individual` decimal(10,2) NOT NULL,
  `preco_pacote5` decimal(10,2) DEFAULT NULL,
  `itens_inclusos` text DEFAULT NULL,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aulas`
--

INSERT INTO `aulas` (`id`, `nivel`, `titulo`, `descricao`, `duracao_minutos`, `max_alunos`, `preco_individual`, `preco_pacote5`, `itens_inclusos`, `disponivel`, `data_cadastro`) VALUES
(1, 'iniciante', 'Aula Inaugural do Surf', 'Perfeita para quem nunca surfou. Do equilíbrio na areia à primeira onda de pé.', 90, 5, 120.00, NULL, 'prancha, lycra', 1, '2026-05-25 16:34:38'),
(2, 'intermediario', 'Evolução Técnica', 'Para quem já ficou em pé e quer evoluir manobras, leitura de ondas e posicionamento. Inclui análise de vídeo pós-aula.', 120, 2, 180.00, 750.00, 'análise de vídeo', 1, '2026-05-25 16:34:38'),
(3, 'avancado', 'Performance Avançada', 'Para quem quer dominar manobras, ganhar velocidade e surfar com consistência. Feedback individualizado.', 120, 1, 220.00, 950.00, 'análise de vídeo detalhada', 1, '2026-05-25 16:34:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `preco_diaria` decimal(10,2) NOT NULL,
  `preco_pix` decimal(10,2) NOT NULL,
  `preco_parcelado` decimal(10,2) NOT NULL,
  `num_parcelas` int(11) NOT NULL DEFAULT 10,
  `estoque` int(11) NOT NULL DEFAULT 1,
  `disponivel` tinyint(1) NOT NULL DEFAULT 1,
  `imagem_url` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipamentos`
--

INSERT INTO `equipamentos` (`id`, `nome`, `categoria`, `marca`, `descricao`, `preco_diaria`, `preco_pix`, `preco_parcelado`, `num_parcelas`, `estoque`, `disponivel`, `imagem_url`, `data_cadastro`) VALUES
(1, 'Raquete de Beach Tennis Heroes The Bull 2026', 'raquete', 'Heroes', 'Raquete profissional com carbono 100%, ideal para todos os níveis.', 390.00, 309.00, 39.00, 10, 8, 1, 'img/raquete-bull-2026.jpg', '2026-05-25 16:01:05'),
(2, 'Raquete de Beach Tennis Heroes Speed 2026', 'raquete', 'Heroes', 'Modelo mais leve da linha Heroes, ótima para iniciantes.', 290.00, 229.00, 29.00, 10, 5, 1, 'img/raquete-speed-2026.jpg', '2026-05-25 16:01:05'),
(3, 'Prancha de Surf Longboard 9\'', 'prancha', 'Rip Curl', 'Prancha longboard ideal para iniciantes e ondas pequenas.', 150.00, 119.00, 15.00, 10, 4, 1, 'img/prancha-longboard.jpg', '2026-05-25 16:01:05'),
(4, 'Prancha de Surf Shortboard 6\'2', 'prancha', 'Rusty', 'Prancha shortboard para surfistas intermediários e avançados.', 180.00, 142.00, 18.00, 10, 3, 1, 'img/prancha-shortboard.jpg', '2026-05-25 16:01:05'),
(5, 'Lycra Manga Longa UV50+', 'lycra', 'Quiksilver', 'Proteção solar completa com tecido leve e confortável.', 60.00, 47.00, 6.00, 10, 10, 1, 'img/lycra-uv50.jpg', '2026-05-25 16:01:05'),
(6, 'Leash de Surf 9\'', 'acessorio', 'FCS', 'Leash resistente para pranchas de 8 a 10 pés.', 35.00, 27.00, 3.50, 10, 12, 1, 'img/leash-9.jpg', '2026-05-25 16:01:05'),
(7, 'Capa de Prancha Boardbag', 'acessorio', 'Creatures', 'Capa acolchoada para transporte e proteção da prancha.', 45.00, 35.00, 4.50, 10, 6, 1, 'img/boardbag.jpg', '2026-05-25 16:01:05'),
(8, 'Kit Beach Tennis Completo', 'kit', 'Heroes', 'Kit com 2 raquetes + 3 bolas + bolsa. Ideal para casais ou amigos.', 420.00, 332.00, 42.00, 10, 3, 1, 'img/kit-beach-tennis.jpg', '2026-05-25 16:01:05'),
(9, 'Raquete de Beach Tennis Heroes The Bull 2026', 'raquete', 'Heroes', 'Raquete profissional com carbono 100%, ideal para todos os níveis.', 390.00, 309.00, 39.00, 10, 8, 1, 'img/raquete-bull-2026.jpg', '2026-05-25 16:02:46'),
(10, 'Raquete de Beach Tennis Heroes Speed 2026', 'raquete', 'Heroes', 'Modelo mais leve da linha Heroes, ótima para iniciantes.', 290.00, 229.00, 29.00, 10, 5, 1, 'img/raquete-speed-2026.jpg', '2026-05-25 16:02:46'),
(11, 'Prancha de Surf Longboard 9\'', 'prancha', 'Rip Curl', 'Prancha longboard ideal para iniciantes e ondas pequenas.', 150.00, 119.00, 15.00, 10, 4, 1, 'img/prancha-longboard.jpg', '2026-05-25 16:02:46'),
(12, 'Prancha de Surf Shortboard 6\'2', 'prancha', 'Rusty', 'Prancha shortboard para surfistas intermediários e avançados.', 180.00, 142.00, 18.00, 10, 3, 1, 'img/prancha-shortboard.jpg', '2026-05-25 16:02:46'),
(13, 'Lycra Manga Longa UV50+', 'lycra', 'Quiksilver', 'Proteção solar completa com tecido leve e confortável.', 60.00, 47.00, 6.00, 10, 10, 1, 'img/lycra-uv50.jpg', '2026-05-25 16:02:46'),
(14, 'Leash de Surf 9\'', 'acessorio', 'FCS', 'Leash resistente para pranchas de 8 a 10 pés.', 35.00, 27.00, 3.50, 10, 12, 1, 'img/leash-9.jpg', '2026-05-25 16:02:46'),
(15, 'Capa de Prancha Boardbag', 'acessorio', 'Creatures', 'Capa acolchoada para transporte e proteção da prancha.', 45.00, 35.00, 4.50, 10, 6, 1, 'img/boardbag.jpg', '2026-05-25 16:02:46'),
(16, 'Kit Beach Tennis Completo', 'kit', 'Heroes', 'Kit com 2 raquetes + 3 bolas + bolsa. Ideal para casais ou amigos.', 420.00, 332.00, 42.00, 10, 3, 1, 'img/kit-beach-tennis.jpg', '2026-05-25 16:02:46');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aula_id` (`aula_id`);

--
-- Índices de tabela `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 10/06/2026 às 04:50
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
(3, 'Carlos Mendes', '11 96666-3333', 2, '2025-06-11', '10:00:00', 1, 'elo', NULL, 180.00, 'confirmado', '2026-05-25 16:40:02'),
(4, 'edu', '19999999999', 1, '2026-06-02', '07:00:00', 3, 'pix', 'num sei', 360.00, 'pendente', '2026-05-25 17:00:20');

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
(1, 'Raquete de Beach Tennis Heroes The Bull 2026', 'raquete', 'Heroes', 'Raquete profissional com carbono 100%, ideal para todos os níveis.', 390.00, 309.00, 39.00, 10, 8, 1, 'IMG/IMGSequip/raquete_de_beach_tennis_heroes_the_bull_2026.webp', '2026-05-25 16:01:05'),
(2, 'Raquete de Beach Tennis Heroes Speed 2026', 'raquete', 'Heroes', 'Modelo mais leve da linha Heroes, ótima para iniciantes.', 290.00, 229.00, 29.00, 10, 5, 1, 'IMG/IMGSequip/raquete-speed.webp', '2026-05-25 16:01:05'),
(3, 'Prancha de Surf Longboard 9\'', 'prancha', 'Rip Curl', 'Prancha longboard ideal para iniciantes e ondas pequenas.', 150.00, 119.00, 15.00, 10, 4, 1, 'IMG/IMGSequip/Prancha-de-Surf-Longboard.jpg', '2026-05-25 16:01:05'),
(4, 'Prancha de Surf Shortboard 6\'2', 'prancha', 'Rusty', 'Prancha shortboard para surfistas intermediários e avançados.', 180.00, 142.00, 18.00, 10, 3, 1, 'IMG/IMGSequip/shortboard.webp', '2026-05-25 16:01:05'),
(5, 'Lycra Manga Longa UV50+', 'lycra', 'Quiksilver', 'Proteção solar completa com tecido leve e confortável.', 60.00, 47.00, 6.00, 10, 10, 1, 'IMG/IMGSequip/camisa-lycra-preta-frente.jpg', '2026-05-25 16:01:05'),
(6, 'Leash de Surf 9\'', 'acessorio', 'FCS', 'Leash resistente para pranchas de 8 a 10 pés.', 35.00, 27.00, 3.50, 10, 12, 1, 'IMG/IMGSequip/leash-desurf.webp', '2026-05-25 16:01:05'),
(7, 'Capa de Prancha Boardbag', 'acessorio', 'Creatures', 'Capa acolchoada para transporte e proteção da prancha.', 45.00, 35.00, 4.50, 10, 6, 1, 'IMG/IMGSequip/capa-prancha.webp', '2026-05-25 16:01:05'),
(8, 'Kit Beach Tennis Completo', 'kit', 'Heroes', 'Kit com 2 raquetes + 3 bolas + bolsa. Ideal para casais ou amigos.', 420.00, 332.00, 42.00, 10, 3, 1, 'IMG/IMGSequip/kit-beachtennis.webp', '2026-05-25 16:01:05'),
(9, 'Cadeira de Praia Dobrável Premium', 'cadeira', 'Mor', 'Cadeira reclinável com estrutura reforçada de alumínio, ideal para longos dias na praia.', 35.00, 28.00, 3.50, 10, 10, 1, 'IMG/IMGSequip/cadeira-premium.jpg', '2026-06-09 23:29:36'),
(10, 'Guarda-Sol Grande UV50', 'acessorio', 'Mor', 'Guarda-sol reforçado com proteção UV50, diâmetro de 2,4m.', 28.00, 22.00, 2.80, 10, 8, 1, 'IMG/IMGSequip/guarda_sol.jpg', '2026-06-09 23:29:36'),
(11, 'Cooler Térmico de 24L', 'acessorio', 'Coleman', 'Caixa térmica com capacidade para 24 latas, mantém o gelo por até 24h.', 25.00, 20.00, 2.50, 10, 6, 1, 'IMG/IMGSequip/cooler.webp', '2026-06-09 23:29:36'),
(12, 'Chinelo Surf Comfort', 'acessorio', 'Havaianas', 'Chinelo confortável e antiderrapante, ideal para entrada e saída do mar.', 15.00, 12.00, 1.50, 10, 15, 1, 'IMG/IMGSequip/chinelo.jpg', '2026-06-09 23:29:36'),
(13, 'Bolsa Térmica de Praia', 'acessorio', 'Mormaii', 'Bolsa térmica espaçosa para levar bebidas e lanches gelados.', 20.00, 16.00, 2.00, 10, 9, 1, 'IMG/IMGSequip/sacola-termica.webp', '2026-06-09 23:29:36'),
(14, 'Toalha de Praia Extra Grande', 'acessorio', 'Buettner', 'Toalha de praia 180x100cm, secagem rápida e tecido macio.', 18.00, 14.00, 1.80, 10, 12, 1, 'IMG/IMGSequip/toalha_de_praia_piscina.webp', '2026-06-09 23:29:36'),
(15, 'Máscara de Mergulho com Snorkel', 'acessorio', 'Cressi', 'Kit de máscara e snorkel para explorar a vida marinha com conforto.', 30.00, 24.00, 3.00, 10, 7, 1, 'IMG/IMGSequip/snorkel.webp', '2026-06-09 23:29:36'),
(16, 'Rede de Frescobol Pro', 'kit', 'Mormaii', 'Kit com 2 raquetes de frescobol e bola, ideal para diversão na areia.', 32.00, 25.00, 3.20, 10, 8, 1, 'IMG/IMGSequip/rede-de-frescobol.webp', '2026-06-09 23:29:36'),
(17, 'Óculos de Sol Polarizado', 'acessorio', 'Ray-Ban', 'Óculos de sol com lentes polarizadas e proteção UV400.', 22.00, 17.00, 2.20, 10, 10, 1, 'IMG/IMGSequip/oculos.jpg', '2026-06-09 23:29:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `contato` varchar(20) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `chave_pix` varchar(100) NOT NULL,
  `status` enum('aguardando_pagamento','pago','cancelado') NOT NULL DEFAULT 'aguardando_pagamento',
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `nome_completo`, `cpf`, `contato`, `valor_total`, `chave_pix`, `status`, `data_pedido`) VALUES
(1, 'Guilherme da Silva Gimenes', '464.913.248-77', '954854455', 17.00, '29.183.475/0001-02', 'aguardando_pagamento', '2026-06-09 23:50:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `equipamento_id` int(11) NOT NULL,
  `nome_equipamento` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `equipamento_id`, `nome_equipamento`, `quantidade`, `preco_unitario`, `subtotal`) VALUES
(1, 1, 17, 'Óculos de Sol Polarizado', 1, 17.00, 17.00);

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
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `equipamento_id` (`equipamento_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`);

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

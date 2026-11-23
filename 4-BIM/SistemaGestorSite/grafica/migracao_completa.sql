-- ============================================================================
-- MIGRAÇÃO COMPLETA E SEGURA — grafica_db
-- Para bancos que já existem. Pode ser executada várias vezes: antes de criar qualquer
-- tabela, coluna, índice ou chave estrangeira, consulta o information_schema.
-- NÃO apaga tabelas, NÃO apaga registros. FAÇA BACKUP (phpMyAdmin > Exportar) ANTES.
-- Corrige: Unknown column 'codigo' / 'tipo' / 'email' ... e a tabela estoque_movimentacoes ausente.
-- ============================================================================
CREATE DATABASE IF NOT EXISTS grafica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grafica_db;

-- 1) Tabelas que não existem (as que já existem são mantidas como estão) --------

CREATE TABLE IF NOT EXISTS usuarios (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(100) NOT NULL,
 login VARCHAR(100) NOT NULL UNIQUE,
 email VARCHAR(150) NULL DEFAULT NULL UNIQUE,
 senha VARCHAR(255) NOT NULL,
 nivel ENUM('administrador','gerente','funcionario') NOT NULL,
 ativo TINYINT(1) NOT NULL DEFAULT 1,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 codigo VARCHAR(60) NULL UNIQUE,
 categoria VARCHAR(80) NULL,
 descricao TEXT NULL,
 quantidade DECIMAL(10,2) NOT NULL DEFAULT 0,
 unidade VARCHAR(20) NOT NULL DEFAULT 'un',
 estoque_minimo DECIMAL(10,2) NOT NULL DEFAULT 0,
 preco DECIMAL(10,2) NOT NULL DEFAULT 0,
 ativo TINYINT(1) NOT NULL DEFAULT 1,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 telefone VARCHAR(30) NULL,
 email VARCHAR(150) NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_clientes_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
 id INT AUTO_INCREMENT PRIMARY KEY,
 cliente_id INT NOT NULL,
 servico VARCHAR(150) NOT NULL,
 descricao_material TEXT NOT NULL,
 quantidade DECIMAL(12,2) NOT NULL,
 data_pedido DATE NOT NULL,
 prazo DATE NOT NULL,
 valor DECIMAL(12,2) NOT NULL DEFAULT 0,
 funcionario_id INT NOT NULL,
 status ENUM('recebido','em_producao','pronto','entregue','cancelado') NOT NULL DEFAULT 'recebido',
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_pedidos_status (status),
 INDEX idx_pedidos_data (data_pedido),
 CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
 CONSTRAINT fk_pedidos_funcionario FOREIGN KEY (funcionario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vendas (
 id INT AUTO_INCREMENT PRIMARY KEY,
 usuario_id INT NOT NULL,
 cliente_id INT NULL,
 cliente VARCHAR(150) NULL,
 total DECIMAL(10,2) NOT NULL DEFAULT 0,
 status ENUM('aberta','em_producao','concluida','cancelada') NOT NULL DEFAULT 'aberta',
 observacoes TEXT NULL,
 tipo ENUM('direta','servico') NOT NULL DEFAULT 'direta',
 pedido_id INT NULL,
 data_venda DATE NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_vendas_pedido (pedido_id),
 INDEX idx_vendas_data (data_venda),
 CONSTRAINT fk_vendas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
 CONSTRAINT fk_vendas_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
 CONSTRAINT fk_vendas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS venda_itens (
 id INT AUTO_INCREMENT PRIMARY KEY,
 venda_id INT NOT NULL,
 produto_id INT NOT NULL,
 quantidade DECIMAL(10,2) NOT NULL,
 preco_unitario DECIMAL(10,2) NOT NULL,
 CONSTRAINT fk_itens_venda FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
 CONSTRAINT fk_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS estoque_movimentacoes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 produto_id INT NOT NULL,
 usuario_id INT NOT NULL,
 tipo ENUM('entrada','saida') NOT NULL,
 quantidade DECIMAL(12,2) NOT NULL,
 observacao VARCHAR(255) NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_mov_produto_data (produto_id, criado_em),
 CONSTRAINT fk_mov_produto FOREIGN KEY (produto_id) REFERENCES produtos(id),
 CONSTRAINT fk_mov_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- 2) Colunas que o PHP usa e que podem faltar em versões antigas ---------------
-- coluna usuarios.email
SELECT IF(COUNT(*)=0, 'ALTER TABLE `usuarios` ADD COLUMN `email` VARCHAR(150) NULL DEFAULT NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='email';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna usuarios.ativo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `usuarios` ADD COLUMN `ativo` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='ativo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna usuarios.criado_em
SELECT IF(COUNT(*)=0, 'ALTER TABLE `usuarios` ADD COLUMN `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='criado_em';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.codigo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `codigo` VARCHAR(60) NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='codigo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.categoria
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `categoria` VARCHAR(80) NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='categoria';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.descricao
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `descricao` TEXT NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='descricao';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.unidade
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `unidade` VARCHAR(20) NOT NULL DEFAULT ''un''', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='unidade';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.estoque_minimo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `estoque_minimo` DECIMAL(10,2) NOT NULL DEFAULT 0', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='estoque_minimo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.preco
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `preco` DECIMAL(10,2) NOT NULL DEFAULT 0', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='preco';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.ativo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `ativo` TINYINT(1) NOT NULL DEFAULT 1', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='ativo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna produtos.criado_em
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD COLUMN `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='criado_em';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.cliente_id
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `cliente_id` INT NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='cliente_id';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.cliente
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `cliente` VARCHAR(150) NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='cliente';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.observacoes
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `observacoes` TEXT NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='observacoes';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.tipo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `tipo` ENUM(''direta'',''servico'') NOT NULL DEFAULT ''direta''', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='tipo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.pedido_id
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `pedido_id` INT NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='pedido_id';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.data_venda
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `data_venda` DATE NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='data_venda';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna vendas.criado_em
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD COLUMN `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='criado_em';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna estoque_movimentacoes.tipo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `estoque_movimentacoes` ADD COLUMN `tipo` ENUM(''entrada'',''saida'') NOT NULL DEFAULT ''entrada''', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='estoque_movimentacoes' AND COLUMN_NAME='tipo';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna estoque_movimentacoes.observacao
SELECT IF(COUNT(*)=0, 'ALTER TABLE `estoque_movimentacoes` ADD COLUMN `observacao` VARCHAR(255) NULL', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='estoque_movimentacoes' AND COLUMN_NAME='observacao';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- coluna estoque_movimentacoes.criado_em
SELECT IF(COUNT(*)=0, 'ALTER TABLE `estoque_movimentacoes` ADD COLUMN `criado_em` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1') INTO @sql FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='estoque_movimentacoes' AND COLUMN_NAME='criado_em';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Ajustes de dados e de definição ----------------------------------------
-- E-mail de usuário pode ser vazio (NULL); '' vira NULL para o UNIQUE não bloquear vários usuários sem e-mail.
ALTER TABLE usuarios MODIFY email VARCHAR(150) NULL DEFAULT NULL;
UPDATE usuarios SET email = NULL WHERE TRIM(email) = '';
-- Produtos: '' em codigo vira NULL (o UNIQUE de código aceita vários NULL).
UPDATE produtos SET codigo = NULL WHERE TRIM(codigo) = '';
UPDATE vendas SET data_venda = DATE(criado_em) WHERE data_venda IS NULL;
-- Vincula vendas de serviço ao cliente do pedido (histórico do cliente).
UPDATE vendas v JOIN pedidos p ON p.id = v.pedido_id SET v.cliente_id = p.cliente_id WHERE v.cliente_id IS NULL AND v.pedido_id IS NOT NULL;

-- 4) Índices e chaves estrangeiras (somente se ainda não existirem) -----------
-- índice produtos.uq_produtos_codigo
SELECT IF(COUNT(*)=0, 'ALTER TABLE `produtos` ADD UNIQUE INDEX `uq_produtos_codigo` (`codigo`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='produtos' AND COLUMN_NAME='codigo' AND SEQ_IN_INDEX=1;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- índice usuarios.uq_usuarios_email
SELECT IF(COUNT(*)=0, 'ALTER TABLE `usuarios` ADD UNIQUE INDEX `uq_usuarios_email` (`email`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND COLUMN_NAME='email' AND SEQ_IN_INDEX=1;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- índice vendas.uq_vendas_pedido
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD UNIQUE INDEX `uq_vendas_pedido` (`pedido_id`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='pedido_id' AND SEQ_IN_INDEX=1;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- índice vendas.idx_vendas_data
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD INDEX `idx_vendas_data` (`data_venda`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND COLUMN_NAME='data_venda' AND SEQ_IN_INDEX=1;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- índice clientes.idx_clientes_nome
SELECT IF(COUNT(*)=0, 'ALTER TABLE `clientes` ADD INDEX `idx_clientes_nome` (`nome`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='clientes' AND COLUMN_NAME='nome' AND SEQ_IN_INDEX=1;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- índice estoque_movimentacoes.idx_mov_produto_data
SELECT IF(COUNT(*)=0, 'ALTER TABLE `estoque_movimentacoes` ADD INDEX `idx_mov_produto_data` (`produto_id`, `criado_em`)', 'SELECT 1') INTO @sql FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='estoque_movimentacoes' AND INDEX_NAME='idx_mov_produto_data';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- chave estrangeira vendas.fk_vendas_pedido
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD CONSTRAINT `fk_vendas_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`)', 'SELECT 1') INTO @sql FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND CONSTRAINT_NAME='fk_vendas_pedido' AND CONSTRAINT_TYPE='FOREIGN KEY';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- chave estrangeira vendas.fk_vendas_cliente
SELECT IF(COUNT(*)=0, 'ALTER TABLE `vendas` ADD CONSTRAINT `fk_vendas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`)', 'SELECT 1') INTO @sql FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vendas' AND CONSTRAINT_NAME='fk_vendas_cliente' AND CONSTRAINT_TYPE='FOREIGN KEY';
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5) Saldo inicial no histórico (somente se o histórico estiver VAZIO) ----------
-- Evita que o histórico de movimentações comece sem explicar o saldo atual dos produtos.
INSERT INTO estoque_movimentacoes(produto_id, usuario_id, tipo, quantidade, observacao)
SELECT p.id, (SELECT MIN(id) FROM usuarios), 'entrada', p.quantidade, 'Saldo inicial importado na migração'
FROM produtos p
WHERE p.quantidade > 0
  AND (SELECT COUNT(*) FROM estoque_movimentacoes) = 0
  AND (SELECT COUNT(*) FROM usuarios) > 0;

-- 6) Conferência (somente leitura) -------------------------------------------
SELECT TABLE_NAME AS tabela, COLUMN_NAME AS coluna, COLUMN_TYPE AS tipo
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('estoque_movimentacoes')
ORDER BY ORDINAL_POSITION;

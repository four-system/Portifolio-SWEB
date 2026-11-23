CREATE DATABASE IF NOT EXISTS grafica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grafica_db;
CREATE TABLE IF NOT EXISTS usuarios (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(100) NOT NULL, login VARCHAR(100) NOT NULL UNIQUE,
 email VARCHAR(150) NULL DEFAULT NULL UNIQUE,
 senha VARCHAR(255) NOT NULL, nivel ENUM('administrador','gerente','funcionario') NOT NULL,
 ativo TINYINT(1) NOT NULL DEFAULT 1, criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS produtos (
 id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(150) NOT NULL, descricao TEXT NULL,
 quantidade DECIMAL(10,2) NOT NULL DEFAULT 0, unidade VARCHAR(20) NOT NULL DEFAULT 'un',
 preco DECIMAL(10,2) NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS vendas (
 id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NOT NULL, cliente VARCHAR(150) NULL,
 total DECIMAL(10,2) NOT NULL DEFAULT 0,
 status ENUM('aberta','em_producao','concluida','cancelada') NOT NULL DEFAULT 'aberta',
 observacoes TEXT NULL, criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
CREATE TABLE IF NOT EXISTS venda_itens (
 id INT AUTO_INCREMENT PRIMARY KEY, venda_id INT NOT NULL, produto_id INT NOT NULL,
 quantidade DECIMAL(10,2) NOT NULL, preco_unitario DECIMAL(10,2) NOT NULL,
 FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
 FOREIGN KEY (produto_id) REFERENCES produtos(id)
);


CREATE TABLE IF NOT EXISTS clientes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 telefone VARCHAR(30) NULL,
 email VARCHAR(150) NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_clientes_nome (nome)
);
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
);

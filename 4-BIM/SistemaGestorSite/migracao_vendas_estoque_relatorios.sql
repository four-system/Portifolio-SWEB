USE grafica_db;
-- Faça backup antes de executar. Execute uma única vez em bancos já existentes.
ALTER TABLE produtos
  ADD COLUMN codigo VARCHAR(60) NULL UNIQUE,
  ADD COLUMN categoria VARCHAR(80) NULL,
  ADD COLUMN estoque_minimo DECIMAL(10,2) NOT NULL DEFAULT 0;

ALTER TABLE vendas
  ADD COLUMN tipo ENUM('direta','servico') NOT NULL DEFAULT 'direta',
  ADD COLUMN pedido_id INT NULL,
  ADD COLUMN data_venda DATE NULL;
ALTER TABLE vendas ADD UNIQUE KEY uq_vendas_pedido (pedido_id);
ALTER TABLE vendas ADD CONSTRAINT fk_vendas_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id);
UPDATE vendas SET data_venda=DATE(criado_em) WHERE data_venda IS NULL;

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
);
-- Registra o saldo atual como saldo inicial no histórico (opcional, evita duplicar movimentos anteriores desconhecidos).
INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao)
SELECT p.id, (SELECT MIN(id) FROM usuarios), 'entrada', p.quantidade, 'Saldo inicial importado na migração'
FROM produtos p WHERE p.quantidade > 0 AND EXISTS (SELECT 1 FROM usuarios);

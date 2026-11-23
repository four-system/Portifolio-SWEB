<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_cargo(['funcionario', 'gerente', 'administrador']);
$uid = (int)$_SESSION['usuario']['id'];
$prod = $pdo->query('SELECT id,nome,codigo,quantidade,unidade,preco FROM produtos WHERE ativo=1 ORDER BY nome')->fetchAll();
$clientes = $pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$pedidos = $pdo->query("SELECT p.id,p.servico,p.valor,p.prazo,c.nome cliente FROM pedidos p JOIN clientes c ON c.id=p.cliente_id LEFT JOIN vendas v ON v.pedido_id=p.id WHERE p.status<>'cancelado' AND v.id IS NULL ORDER BY p.prazo,p.id DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $tipo = $_POST['tipo'] ?? 'direta'; $obs = trim($_POST['observacoes'] ?? ''); $data = $_POST['data_venda'] ?? date('Y-m-d');
    try {
        if (!in_array($tipo, ['direta', 'servico'], true)) throw new Exception('Tipo de venda inválido.');
        if (!data_valida($data)) throw new Exception('Informe uma data de venda válida.');
        $pdo->beginTransaction(); $itens = []; $total = 0.0; $pedidoId = null; $clienteId = null; $clienteNome = null;
        if ($tipo === 'direta') {
            $clienteId = (int)($_POST['cliente_id'] ?? 0) ?: null;
            if ($clienteId) {
                $q = $pdo->prepare('SELECT nome FROM clientes WHERE id=?'); $q->execute([$clienteId]); $c = $q->fetch();
                if (!$c) throw new Exception('Cliente selecionado não encontrado.');
                $clienteNome = $c['nome'];
            } else { $clienteNome = trim($_POST['cliente'] ?? '') ?: null; if ($clienteNome !== null && mb_strlen($clienteNome) > 150) throw new Exception('Nome do cliente muito longo.'); }
            $ids = $_POST['produto'] ?? []; $qts = $_POST['quantidade'] ?? []; $precos = $_POST['preco_unitario'] ?? [];
            if (!is_array($ids) || !is_array($qts) || !is_array($precos)) throw new Exception('Dados da venda inválidos.');
            $reservado = [];
            foreach ($ids as $i => $id) {
                $qt = num_post($qts[$i] ?? '0'); if ($qt <= 0) continue;
                $q = $pdo->prepare('SELECT * FROM produtos WHERE id=? AND ativo=1 FOR UPDATE'); $q->execute([(int)$id]); $p = $q->fetch();
                if (!$p) throw new Exception('Um dos produtos selecionados não está disponível.');
                $reservado[$p['id']] = ($reservado[$p['id']] ?? 0) + $qt;
                if ((float)$p['quantidade'] < $reservado[$p['id']]) throw new Exception('Estoque insuficiente para ' . $p['nome'] . '. Disponível: ' . $p['quantidade'] . ' ' . $p['unidade'] . '.');
                $preco = num_post($precos[$i] ?? $p['preco']); if ($preco < 0) throw new Exception('Preço unitário inválido.');
                $total += $qt * $preco; $itens[] = [$p, $qt, $preco];
            }
            if (!$itens) throw new Exception('Adicione pelo menos um produto à venda (quantidade maior que zero).');
        } else {
            $pedidoId = (int)($_POST['pedido_id'] ?? 0);
            $q = $pdo->prepare("SELECT p.*,c.nome cliente FROM pedidos p JOIN clientes c ON c.id=p.cliente_id WHERE p.id=? AND p.status<>'cancelado' FOR UPDATE"); $q->execute([$pedidoId]); $p = $q->fetch();
            if (!$p) throw new Exception('Pedido gráfico não encontrado ou cancelado.');
            $q = $pdo->prepare('SELECT id FROM vendas WHERE pedido_id=?'); $q->execute([$pedidoId]); if ($q->fetch()) throw new Exception('Este pedido já foi faturado.');
            $total = (float)$p['valor']; $clienteNome = $p['cliente']; $clienteId = (int)$p['cliente_id'];
        }
        $total = round($total, 2);
        $pdo->prepare("INSERT INTO vendas(usuario_id,cliente_id,cliente,total,status,observacoes,tipo,pedido_id,data_venda) VALUES(?,?,?,?,'aberta',?,?,?,?)")->execute([$uid, $clienteId, $clienteNome, $total, $obs, $tipo, $pedidoId, $data]);
        $vid = (int)$pdo->lastInsertId();
        foreach ($itens as [$p, $qt, $preco]) {
            $pdo->prepare('INSERT INTO venda_itens(venda_id,produto_id,quantidade,preco_unitario) VALUES(?,?,?,?)')->execute([$vid, $p['id'], $qt, $preco]);
            $pdo->prepare('UPDATE produtos SET quantidade=quantidade-? WHERE id=?')->execute([$qt, $p['id']]);
            $pdo->prepare("INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,'saida',?,?)")->execute([$p['id'], $uid, $qt, 'Venda direta #' . $vid]);
        }
        $pdo->commit(); flash('Venda #' . $vid . ' registrada com sucesso. Total: R$ ' . number_format($total, 2, ',', '.')); header('Location: vendas.php'); exit;
    } catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Nova venda: ' . $e->getMessage());

    die(
        '<h3>Erro ao registrar venda:</h3><pre>' .
        htmlspecialchars($e->getMessage()) .
        '</pre>'
    );
}

    
}
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Nova venda</title><link rel="stylesheet" href="style.css"><script>
function alternarTipo(){const t=document.getElementById('tipo').value;document.getElementById('bloco-direta').hidden=t!=='direta';document.getElementById('bloco-servico').hidden=t!=='servico';}
function somar(){let total=0;document.querySelectorAll('.item-venda').forEach(el=>{const q=parseFloat(el.querySelector('[name="quantidade[]"]').value)||0;const p=parseFloat(el.querySelector('[name="preco_unitario[]"]').value)||0;total+=q*p;});document.getElementById('total-previsto').textContent=total.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});}
</script></head><body><main class="app"><header class="topbar"><h2>Registrar venda</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash(); ?>
<form class="form-box" method="post"><?= csrf_field() ?><label>Tipo de operação *</label><select id="tipo" name="tipo" onchange="alternarTipo()"><option value="direta">Venda direta de produtos</option><option value="servico">Faturamento de pedido gráfico</option></select>
<div id="bloco-direta"><label>Cliente cadastrado (opcional)</label><select name="cliente_id"><option value="">Consumidor avulso</option><?php foreach ($clientes as $c): ?><option value="<?= (int)$c['id'] ?>"><?= h($c['nome']) ?></option><?php endforeach; ?></select><label>ou nome do cliente avulso (opcional)</label><input name="cliente" maxlength="150" placeholder="Nome do cliente"><h2>Produtos vendidos</h2><?php if (!$prod): ?><p>Nenhum produto ativo cadastrado no estoque.</p><?php endif; ?><?php foreach ($prod as $x): ?><div class="sale-item item-venda"><div><strong><?= h($x['nome']) ?></strong><small><?= h($x['codigo'] ?: 'Sem código') ?> · Disponível: <?= h($x['quantidade']) ?> <?= h($x['unidade']) ?></small></div><div><label>Quantidade</label><input type="number" name="quantidade[]" min="0" max="<?= h($x['quantidade']) ?>" step="0.01" value="0" oninput="somar()"></div><div><label>Valor unitário</label><input type="number" name="preco_unitario[]" min="0" step="0.01" value="<?= h($x['preco']) ?>" oninput="somar()"></div><input type="hidden" name="produto[]" value="<?= (int)$x['id'] ?>"></div><?php endforeach; ?><p><strong>Total calculado: <span id="total-previsto">R$ 0,00</span></strong></p></div>
<div id="bloco-servico" hidden><label>Pedido gráfico a faturar *</label><select name="pedido_id"><option value="">Selecione um pedido</option><?php foreach ($pedidos as $p): ?><option value="<?= (int)$p['id'] ?>">#<?= (int)$p['id'] ?> · <?= h($p['cliente']) ?> · <?= h($p['servico']) ?> · R$ <?= number_format((float)$p['valor'], 2, ',', '.') ?> · Prazo <?= date('d/m/Y', strtotime($p['prazo'])) ?></option><?php endforeach; ?></select><?php if (!$pedidos): ?><p>Não há pedidos disponíveis para faturamento. Pedidos cancelados ou já faturados não aparecem.</p><?php endif; ?><p>O faturamento de serviço gráfico usa o valor do pedido e não movimenta o estoque de produtos automaticamente.</p></div>
<label>Data da venda *</label><input type="date" name="data_venda" required value="<?= date('Y-m-d') ?>"><label>Observações</label><textarea name="observacoes"></textarea><button>Registrar operação</button></form></section></main><script>alternarTipo();</script></body></html>

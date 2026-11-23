<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
require_once 'config/relatorios_dados.php';
exigir_cargo(['administrador', 'gerente', 'funcionario']);
$nivel = $_SESSION['usuario']['nivel']; $uid = (int)$_SESSION['usuario']['id'];
$statusVenda = ['aberta', 'em_producao', 'concluida', 'cancelada'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    if (!in_array($nivel, ['administrador', 'gerente'], true)) { http_response_code(403); exit('Acesso não autorizado. <a href="vendas.php">Voltar</a>'); }
    $st = $_POST['status'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if (!in_array($st, $statusVenda, true)) throw new Exception('Status inválido.');
        $pdo->beginTransaction(); $q = $pdo->prepare('SELECT * FROM vendas WHERE id=? FOR UPDATE'); $q->execute([$id]); $v = $q->fetch();
        if (!$v) throw new Exception('Venda não encontrada.');
        if ($v['status'] === 'cancelada' && $st !== 'cancelada') throw new Exception('Uma venda cancelada não pode ser reaberta.');
        if ($v['status'] !== 'cancelada' && $st === 'cancelada') {
            if ($v['tipo'] === 'direta') {
                // Devolve os itens ao estoque e registra o estorno no histórico.
                $q = $pdo->prepare('SELECT vi.* FROM venda_itens vi WHERE vi.venda_id=?'); $q->execute([$id]);
                foreach ($q->fetchAll() as $item) {
                    $pdo->prepare('UPDATE produtos SET quantidade=quantidade+? WHERE id=?')->execute([$item['quantidade'], $item['produto_id']]);
                    $pdo->prepare("INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,'entrada',?,?)")->execute([$item['produto_id'], $uid, $item['quantidade'], 'Estorno da venda cancelada #' . $id]);
                }
            } elseif ($v['pedido_id']) {
                // Libera o pedido para ser faturado de novo (pedido_id é único) e guarda a referência nas observações.
                $nota = trim(($v['observacoes'] ?? '') . ' [Cancelada; estava vinculada ao pedido #' . $v['pedido_id'] . ']');
                $pdo->prepare('UPDATE vendas SET pedido_id=NULL, observacoes=? WHERE id=?')->execute([$nota, $id]);
            }
        }
        $pdo->prepare('UPDATE vendas SET status=? WHERE id=?')->execute([$st, $id]); $pdo->commit(); flash('Status da venda atualizado.');
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); flash_erro($e instanceof PDOException ? 'Não foi possível atualizar a venda.' : $e->getMessage()); }
    header('Location: vendas.php'); exit;
}

$inicio = $_GET['inicio'] ?? ''; $fim = $_GET['fim'] ?? ''; $fStatus = $_GET['status'] ?? '';
$where = []; $args = [];
$dataExpr = 'COALESCE(v.data_venda,DATE(v.criado_em))';
if ($nivel === 'funcionario') { $where[] = 'v.usuario_id=?'; $args[] = $uid; }
if ($inicio !== '' && data_valida($inicio)) { $where[] = "$dataExpr>=?"; $args[] = $inicio; } else $inicio = '';
if ($fim !== '' && data_valida($fim)) { $where[] = "$dataExpr<=?"; $args[] = $fim; } else $fim = '';
if (in_array($fStatus, $statusVenda, true)) { $where[] = 'v.status=?'; $args[] = $fStatus; } else $fStatus = '';
$sql = 'SELECT v.*,u.nome AS vendedor,p.servico AS pedido_servico FROM vendas v JOIN usuarios u ON u.id=v.usuario_id LEFT JOIN pedidos p ON p.id=v.pedido_id' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY $dataExpr DESC,v.id DESC";
$q = $pdo->prepare($sql); $q->execute($args); $vendas = $q->fetchAll();
$itensQ = $pdo->prepare('SELECT vi.*,p.nome,p.unidade FROM venda_itens vi JOIN produtos p ON p.id=vi.produto_id WHERE vi.venda_id=?');
$somaTotal = 0.0; foreach ($vendas as $v) if ($v['status'] !== 'cancelada') $somaTotal += (float)$v['total'];
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Vendas</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Vendas e faturamentos</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash(); ?><a class="button-link" href="nova_venda.php">+ Nova venda / faturamento</a>
<form class="form-box" method="get"><div class="form-row"><div><label>De</label><input type="date" name="inicio" value="<?= h($inicio) ?>"></div><div><label>Até</label><input type="date" name="fim" value="<?= h($fim) ?>"></div><div><label>Status</label><select name="status"><option value="">Todos</option><?php foreach ($statusVenda as $s): ?><option value="<?= h($s) ?>" <?= $fStatus === $s ? 'selected' : '' ?>><?= h(rotulo_status_venda($s)) ?></option><?php endforeach; ?></select></div></div><button>Filtrar</button> <a href="vendas.php">Limpar</a></form>
<p class="subtitle"><?= count($vendas) ?> registro(s) · Total não cancelado: <strong>R$ <?= number_format($somaTotal, 2, ',', '.') ?></strong></p>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Tipo</th><th>Cliente / pedido</th><th>Itens</th><th>Responsável</th><th>Total</th><th>Status</th><th>Data</th><?php if ($nivel !== 'funcionario'): ?><th>Atualizar</th><?php endif; ?></tr></thead><tbody><?php foreach ($vendas as $v): ?><tr><td>#<?= (int)$v['id'] ?></td><td><?= $v['tipo'] === 'servico' ? 'Serviço gráfico' : 'Venda direta' ?></td><td><?= h($v['cliente'] ?: '—') ?><?php if ($v['pedido_id']): ?><br><small>Pedido #<?= (int)$v['pedido_id'] ?> · <?= h($v['pedido_servico']) ?></small><?php endif; ?><?php if (!empty($v['observacoes'])): ?><br><small><?= h($v['observacoes']) ?></small><?php endif; ?></td><td><?php if ($v['tipo'] === 'servico'): ?>Faturamento de serviço<?php else: $itensQ->execute([$v['id']]); foreach ($itensQ->fetchAll() as $it): ?><div><?= h($it['nome']) ?> — <?= h($it['quantidade']) ?> <?= h($it['unidade']) ?> × R$ <?= number_format((float)$it['preco_unitario'], 2, ',', '.') ?></div><?php endforeach; endif; ?></td><td><?= h($v['vendedor']) ?></td><td>R$ <?= number_format((float)$v['total'], 2, ',', '.') ?></td><td><?= h(rotulo_status_venda($v['status'])) ?></td><td><?= date('d/m/Y', strtotime($v['data_venda'] ?: $v['criado_em'])) ?></td><?php if ($nivel !== 'funcionario'): ?><td><?php if ($v['status'] !== 'cancelada'): ?><form method="post" class="inline" onsubmit="return this.status.value!=='cancelada'||confirm('Cancelar esta venda? Não é possível reabrir.')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><select name="status"><?php foreach (['aberta', 'em_producao', 'concluida'] as $s): ?><option value="<?= $s ?>" <?= $v['status'] === $s ? 'selected' : '' ?>><?= h(rotulo_status_venda($s)) ?></option><?php endforeach; ?><option value="cancelada">Cancelar</option></select><button class="mini">Salvar</button></form><?php else: ?>Cancelada<?php endif; ?></td><?php endif; ?></tr><?php endforeach; ?><?php if (!$vendas): ?><tr><td colspan="9">Nenhuma venda encontrada.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>

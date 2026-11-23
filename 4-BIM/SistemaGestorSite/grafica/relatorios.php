<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
require_once 'config/relatorios_dados.php';
exigir_cargo(['administrador', 'gerente']);
$inicio = $_GET['inicio'] ?? date('Y-m-01'); $fim = $_GET['fim'] ?? date('Y-m-d');
$aviso = '';
if (!periodo_valido($inicio, $fim)) { $aviso = 'Período inválido (datas incorretas ou data inicial maior que a final). Mostrando o mês atual.'; $inicio = date('Y-m-01'); $fim = date('Y-m-d'); }
$d = dados_relatorio($pdo, $inicio, $fim);
$ped = $d['ped'];
$tabela = function (array $cab, array $linhas, string $vazio) {
    echo '<div class="table-wrap"><table><thead><tr>';
    foreach ($cab as $c) echo '<th>' . h($c) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($linhas as $l) { echo '<tr>'; foreach ($l as $c) echo '<td>' . h($c) . '</td>'; echo '</tr>'; }
    if (!$linhas) echo '<tr><td colspan="' . count($cab) . '">' . h($vazio) . '</td></tr>';
    echo '</tbody></table></div>';
};
$moeda = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Relatórios gerenciais</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Relatórios gerenciais</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash(); if ($aviso) echo '<div class="alerta erro">' . h($aviso) . '</div>'; ?><h1>Indicadores da gráfica</h1><form class="form-box" method="get"><div class="form-row"><div><label>Data inicial</label><input type="date" name="inicio" value="<?= h($inicio) ?>" required></div><div><label>Data final</label><input type="date" name="fim" value="<?= h($fim) ?>" required></div><div style="align-self:end"><button>Aplicar período</button></div></div><a class="button-link" href="relatorio_pdf.php?inicio=<?= urlencode($inicio) ?>&fim=<?= urlencode($fim) ?>">Gerar relatório PDF</a></form>
<div class="cards"><article class="module-card"><h2><?= (int)$ped['total'] ?></h2><p>Pedidos recebidos no período</p></article><article class="module-card"><h2><?= (int)$ped['concluidos'] ?></h2><p>Pedidos concluídos (prontos ou entregues)</p></article><article class="module-card"><h2><?= $d['qtd_vendas'] ?></h2><p>Vendas/faturamentos no período</p></article><article class="module-card"><h2><?= $moeda($d['faturamento']) ?></h2><p>Faturamento (vendas não canceladas)</p></article><article class="module-card"><h2><?= count($d['baixo']) ?></h2><p>Produtos com estoque baixo</p></article></div>
<h2>Pedidos por status</h2><?php $tabela(['Status', 'Quantidade', 'Valor'], array_map(fn($x) => [rotulo_status_pedido($x['status']), $x['quantidade'], $moeda($x['valor'])], $d['ped_status']), 'Sem pedidos no período.'); ?>
<h2>Vendas por tipo</h2><?php $tabela(['Tipo', 'Quantidade', 'Valor total'], array_map(fn($x) => [$x['tipo'] === 'servico' ? 'Serviços gráficos' : 'Vendas diretas', $x['quantidade'], $moeda($x['valor'])], $d['vendas_tipo']), 'Sem vendas no período.'); ?>
<p class="subtitle">Vendas canceladas no período: <?= (int)$d['vendas_canceladas']['quantidade'] ?> (<?= $moeda($d['vendas_canceladas']['valor']) ?>) — não entram no faturamento.</p>
<h2>Vendas do período</h2><?php $tabela(['Nº', 'Data', 'Tipo', 'Cliente', 'Total', 'Status'], array_map(fn($x) => ['#' . $x['id'], date('d/m/Y', strtotime($x['data'])), $x['tipo'] === 'servico' ? 'Serviço' : 'Direta', $x['cliente'] ?: '—', $moeda($x['total']), rotulo_status_venda($x['status'])], $d['vendas']), 'Sem vendas no período.'); ?>
<h2>Produtos vendidos no período</h2><?php $tabela(['Produto', 'Código', 'Quantidade', 'Valor'], array_map(fn($x) => [$x['nome'], $x['codigo'] ?: '—', $x['qtd'] . ' ' . $x['unidade'], $moeda($x['valor'])], $d['produtos_vendidos']), 'Nenhum produto vendido no período.'); ?>
<h2>Movimentações de estoque no período</h2><?php $tabela(['Tipo', 'Nº de movimentos', 'Quantidade total'], array_map(fn($x) => [($x['tipo']==='saida'?'Saída':'Entrada'), $x['movimentos'], number_format((float)$x['quantidade'], 2, ',', '.')], $d['movimentos']), 'Sem movimentações no período.'); ?>
<h2>Produtos com estoque baixo</h2><?php $tabela(['Produto', 'Código', 'Categoria', 'Disponível', 'Mínimo'], array_map(fn($p) => [$p['nome'], $p['codigo'] ?: '—', $p['categoria'] ?: '—', $p['quantidade'] . ' ' . $p['unidade'], $p['estoque_minimo'] . ' ' . $p['unidade']], $d['baixo']), 'Nenhum produto abaixo do estoque mínimo.'); ?>
<h2>Estoque atual (produtos ativos)</h2><?php $tabela(['Produto', 'Código', 'Categoria', 'Disponível', 'Preço'], array_map(fn($p) => [$p['nome'], $p['codigo'] ?: '—', $p['categoria'] ?: '—', $p['quantidade'] . ' ' . $p['unidade'], $moeda($p['preco'])], $d['estoque']), 'Nenhum produto ativo.'); ?>
<p class="subtitle">Período analisado: <?= date('d/m/Y', strtotime($inicio)) ?> a <?= date('d/m/Y', strtotime($fim)) ?>. Pedidos são filtrados pela data do pedido; vendas pela data da venda; movimentações pela data do registro. O alerta de estoque considera quantidade menor ou igual ao mínimo.</p></section></main></body></html>

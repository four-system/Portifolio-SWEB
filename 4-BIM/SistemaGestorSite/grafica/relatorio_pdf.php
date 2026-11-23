<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
require_once 'config/pdf.php';
require_once 'config/relatorios_dados.php';
exigir_cargo(['administrador', 'gerente']);
$inicio = $_GET['inicio'] ?? date('Y-m-01'); $fim = $_GET['fim'] ?? date('Y-m-d');
if (!periodo_valido($inicio, $fim)) { http_response_code(400); exit('Período inválido.'); }
$d = dados_relatorio($pdo, $inicio, $fim); $ped = $d['ped'];
$m = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');
$L = ['RELATÓRIO GERENCIAL - GRÁFICA', 'Gerado em: ' . date('d/m/Y H:i'), 'Período: ' . date('d/m/Y', strtotime($inicio)) . ' até ' . date('d/m/Y', strtotime($fim)), '',
    'PEDIDOS GRÁFICOS',
    'Pedidos recebidos no período: ' . (int)$ped['total'],
    'Pedidos concluídos (prontos ou entregues): ' . (int)$ped['concluidos'],
    'Pedidos cancelados: ' . (int)$ped['cancelados']];
foreach ($d['ped_status'] as $s) $L[] = '  ' . rotulo_status_pedido($s['status']) . ': ' . (int)$s['quantidade'] . ' pedido(s) - ' . $m($s['valor']);
array_push($L, '', 'VENDAS E FATURAMENTO', 'Quantidade de vendas/faturamentos: ' . $d['qtd_vendas'], 'Faturamento (não cancelado): ' . $m($d['faturamento']),
    'Vendas canceladas (fora do faturamento): ' . (int)$d['vendas_canceladas']['quantidade'] . ' - ' . $m($d['vendas_canceladas']['valor']));
foreach ($d['vendas_tipo'] as $v) $L[] = '  ' . ($v['tipo'] === 'servico' ? 'Serviços gráficos' : 'Vendas diretas') . ': ' . (int)$v['quantidade'] . ' operações - ' . $m($v['valor']);
array_push($L, '', 'VENDAS DO PERÍODO', 'Nº | Data | Tipo | Cliente | Total | Status');
if (!$d['vendas']) $L[] = 'Sem vendas no período.';
foreach ($d['vendas'] as $v) $L[] = '#' . $v['id'] . ' | ' . date('d/m/Y', strtotime($v['data'])) . ' | ' . ($v['tipo'] === 'servico' ? 'Serviço' : 'Direta') . ' | ' . mb_substr((string)($v['cliente'] ?: '-'), 0, 30) . ' | ' . $m($v['total']) . ' | ' . rotulo_status_venda($v['status']);
array_push($L, '', 'PRODUTOS VENDIDOS NO PERÍODO');
if (!$d['produtos_vendidos']) $L[] = 'Nenhum produto vendido no período.';
foreach ($d['produtos_vendidos'] as $p) $L[] = mb_substr($p['nome'], 0, 40) . ' | ' . ($p['codigo'] ?: '-') . ' | ' . $p['qtd'] . ' ' . $p['unidade'] . ' | ' . $m($p['valor']);
array_push($L, '', 'MOVIMENTAÇÕES DE ESTOQUE NO PERÍODO');
if (!$d['movimentos']) $L[] = 'Sem movimentações no período.';
foreach ($d['movimentos'] as $x) $L[] = ($x['tipo']==='saida'?'Saída':'Entrada') . ': ' . (int)$x['movimentos'] . ' movimento(s), total ' . number_format((float)$x['quantidade'], 2, ',', '.');
array_push($L, '', 'PRODUTOS COM ESTOQUE BAIXO', 'Produto | Código | Categoria | Disponível | Mínimo');
if (!$d['baixo']) $L[] = 'Nenhum produto abaixo do estoque mínimo.';
foreach ($d['baixo'] as $p) $L[] = mb_substr($p['nome'], 0, 28) . ' | ' . ($p['codigo'] ?: '-') . ' | ' . ($p['categoria'] ?: '-') . ' | ' . $p['quantidade'] . ' ' . $p['unidade'] . ' | ' . $p['estoque_minimo'] . ' ' . $p['unidade'];
array_push($L, '', 'ESTOQUE ATUAL (produtos ativos)', 'Produto | Código | Disponível | Preço');
if (!$d['estoque']) $L[] = 'Nenhum produto ativo.';
foreach ($d['estoque'] as $p) $L[] = mb_substr($p['nome'], 0, 40) . ' | ' . ($p['codigo'] ?: '-') . ' | ' . $p['quantidade'] . ' ' . $p['unidade'] . ' | ' . $m($p['preco']);
array_push($L, '', 'Alerta: estoque baixo significa quantidade menor ou igual ao mínimo cadastrado.');
pdf_relatorio($L, 'relatorio_grafica_' . $inicio . '_a_' . $fim . '.pdf');

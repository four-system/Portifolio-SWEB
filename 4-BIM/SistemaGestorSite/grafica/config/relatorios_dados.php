<?php
/** Consultas compartilhadas pela tela de relatórios e pelo PDF (garante que os dois mostrem os mesmos números). */

function periodo_valido(string $ini, string $fim): bool {
    return data_valida($ini) && data_valida($fim) && $ini <= $fim;
}

function dados_relatorio(PDO $pdo, string $ini, string $fim): array {
    $r = [];
    $dataVenda = 'COALESCE(v.data_venda, DATE(v.criado_em))';

    $q = $pdo->prepare("SELECT COUNT(*) total, SUM(status IN ('pronto','entregue')) concluidos, SUM(status='cancelado') cancelados,
                               SUM(CASE WHEN status<>'cancelado' THEN valor ELSE 0 END) valor
                        FROM pedidos WHERE data_pedido BETWEEN ? AND ?");
    $q->execute([$ini, $fim]);
    $r['ped'] = $q->fetch();

    $q = $pdo->prepare("SELECT status, COUNT(*) quantidade, SUM(valor) valor FROM pedidos WHERE data_pedido BETWEEN ? AND ? GROUP BY status ORDER BY status");
    $q->execute([$ini, $fim]);
    $r['ped_status'] = $q->fetchAll();

    $q = $pdo->prepare("SELECT v.tipo, COUNT(*) quantidade, SUM(v.total) valor FROM vendas v
                        WHERE $dataVenda BETWEEN ? AND ? AND v.status<>'cancelada' GROUP BY v.tipo");
    $q->execute([$ini, $fim]);
    $r['vendas_tipo'] = $q->fetchAll();
    $r['qtd_vendas'] = 0; $r['faturamento'] = 0.0;
    foreach ($r['vendas_tipo'] as $v) { $r['qtd_vendas'] += (int)$v['quantidade']; $r['faturamento'] += (float)$v['valor']; }

    $q = $pdo->prepare("SELECT COUNT(*) quantidade, COALESCE(SUM(v.total),0) valor FROM vendas v WHERE $dataVenda BETWEEN ? AND ? AND v.status='cancelada'");
    $q->execute([$ini, $fim]);
    $r['vendas_canceladas'] = $q->fetch();

    $q = $pdo->prepare("SELECT v.id, v.tipo, v.cliente, v.total, v.status, $dataVenda AS data
                        FROM vendas v WHERE $dataVenda BETWEEN ? AND ? ORDER BY $dataVenda, v.id");
    $q->execute([$ini, $fim]);
    $r['vendas'] = $q->fetchAll();

    $q = $pdo->prepare("SELECT p.id, p.nome, p.codigo, p.unidade, SUM(vi.quantidade) qtd, SUM(vi.quantidade*vi.preco_unitario) valor
                        FROM venda_itens vi JOIN vendas v ON v.id=vi.venda_id JOIN produtos p ON p.id=vi.produto_id
                        WHERE v.status<>'cancelada' AND $dataVenda BETWEEN ? AND ?
                        GROUP BY p.id, p.nome, p.codigo, p.unidade ORDER BY valor DESC");
    $q->execute([$ini, $fim]);
    $r['produtos_vendidos'] = $q->fetchAll();

    $q = $pdo->prepare("SELECT tipo, COUNT(*) movimentos, SUM(quantidade) quantidade FROM estoque_movimentacoes
                        WHERE DATE(criado_em) BETWEEN ? AND ? GROUP BY tipo");
    $q->execute([$ini, $fim]);
    $r['movimentos'] = $q->fetchAll();

    $r['estoque'] = $pdo->query('SELECT id,nome,codigo,categoria,quantidade,estoque_minimo,unidade,preco FROM produtos WHERE ativo=1 ORDER BY nome')->fetchAll();
    $r['baixo'] = array_values(array_filter($r['estoque'], fn($p) => (float)$p['quantidade'] <= (float)$p['estoque_minimo']));
    return $r;
}

function rotulo_status_pedido(string $s): string {
    return ['recebido' => 'Recebido', 'em_producao' => 'Em produção', 'pronto' => 'Pronto', 'entregue' => 'Entregue', 'cancelado' => 'Cancelado'][$s] ?? $s;
}
function rotulo_status_venda(string $s): string {
    return ['aberta' => 'Aberta', 'em_producao' => 'Em produção', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada'][$s] ?? $s;
}

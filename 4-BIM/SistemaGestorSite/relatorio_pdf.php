<?php
session_start(); require_once 'config/conexao.php'; require_once 'config/auth.php'; require_once 'config/pdf.php'; exigir_cargo(['administrador','gerente']);
$inicio=$_GET['inicio']??date('Y-m-01');$fim=$_GET['fim']??date('Y-m-d');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$inicio)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fim)||$inicio>$fim){http_response_code(400);exit('Período inválido.');}
$q=$pdo->prepare("SELECT COUNT(*) total,SUM(status IN ('pronto','entregue')) concluidos,SUM(status='cancelado') cancelados FROM pedidos WHERE data_pedido BETWEEN ? AND ?");$q->execute([$inicio,$fim]);$ped=$q->fetch();
$q=$pdo->prepare("SELECT COALESCE(tipo,'direta') tipo,COUNT(*) quantidade,SUM(total) valor FROM vendas WHERE COALESCE(data_venda,DATE(criado_em)) BETWEEN ? AND ? AND status<>'cancelada' GROUP BY COALESCE(tipo,'direta')");$q->execute([$inicio,$fim]);$vendas=$q->fetchAll();$qtd=0;$faturamento=0;foreach($vendas as $v){$qtd+=(int)$v['quantidade'];$faturamento+=(float)$v['valor'];}
$baixo=$pdo->query('SELECT nome,codigo,categoria,quantidade,estoque_minimo,unidade FROM produtos WHERE ativo=1 AND quantidade<=estoque_minimo ORDER BY quantidade,nome')->fetchAll();
$linhas=['RELATORIO GERENCIAL - GRAFICA','Gerado em: '.date('d/m/Y H:i'),'Periodo: '.date('d/m/Y',strtotime($inicio)).' ate '.date('d/m/Y',strtotime($fim)),'','PEDIDOS GRAFICOS','Pedidos recebidos no periodo: '.(int)$ped['total'],'Pedidos concluidos (prontos ou entregues): '.(int)$ped['concluidos'],'Pedidos cancelados: '.(int)$ped['cancelados'],'','VENDAS E FATURAMENTO','Quantidade de vendas/faturamentos: '.$qtd,'Faturamento nao cancelado: R$ '.number_format($faturamento,2,',','.')];
foreach($vendas as $v)$linhas[]=($v['tipo']==='servico'?'Servicos graficos':'Vendas diretas').': '.(int)$v['quantidade'].' operacoes - R$ '.number_format((float)$v['valor'],2,',','.');
$linhas[]='';$linhas[]='PRODUTOS COM ESTOQUE BAIXO';$linhas[]='Produto | Codigo | Categoria | Disponivel | Minimo';
if(!$baixo)$linhas[]='Nenhum produto abaixo do estoque minimo.';
foreach($baixo as $p)$linhas[]=substr($p['nome'],0,28).' | '.($p['codigo']?:'-').' | '.($p['categoria']?:'-').' | '.$p['quantidade'].' '.$p['unidade'].' | '.$p['estoque_minimo'].' '.$p['unidade'];
$linhas[]='';$linhas[]='Alerta: estoque baixo significa quantidade menor ou igual ao minimo cadastrado.';
pdf_relatorio($linhas,'relatorio_grafica_'.$inicio.'_a_'.$fim.'.pdf');

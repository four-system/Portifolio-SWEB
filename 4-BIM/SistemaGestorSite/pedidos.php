<?php
session_start(); require_once 'config/conexao.php'; require_once 'config/auth.php'; exigir_login();
$statuses=['recebido'=>'Recebido','em_producao'=>'Em produção','pronto'=>'Pronto','entregue'=>'Entregue','cancelado'=>'Cancelado'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0); $st=$_POST['status']??'';
    if($id>0 && isset($statuses[$st])) { $q=$pdo->prepare('UPDATE pedidos SET status=? WHERE id=?'); $q->execute([$st,$id]); flash('Andamento do pedido atualizado.'); }
    else flash('Status inválido.');
    header('Location: pedidos.php'); exit;
}
$numero=trim($_GET['numero']??''); $cliente=trim($_GET['cliente']??''); $status=$_GET['status']??'';
$sql='SELECT p.*,c.nome AS cliente_nome,u.nome AS responsavel FROM pedidos p JOIN clientes c ON c.id=p.cliente_id JOIN usuarios u ON u.id=p.funcionario_id WHERE 1=1'; $args=[];
if($numero!=='' && ctype_digit($numero)){ $sql.=' AND p.id=?'; $args[]=(int)$numero; }
elseif($numero!==''){ $sql.=' AND 1=0'; }
if($cliente!==''){ $sql.=' AND c.nome LIKE ?'; $args[]='%'.$cliente.'%'; }
if(isset($statuses[$status])){ $sql.=' AND p.status=?'; $args[]=$status; }
$sql.=' ORDER BY p.data_pedido DESC,p.id DESC'; $q=$pdo->prepare($sql); $q->execute($args); $pedidos=$q->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pedidos gráficos</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Pedidos gráficos</h2><div class="userbox"><a class="link-sair" href="novo_pedido.php">+ Novo pedido</a><a class="link-sair" href="painel.php">← Painel</a></div></header><section class="content"><?php mostrar_flash();?><h1>Acompanhamento de pedidos</h1><form class="form-box" method="get"><div class="form-row"><div><label>Número do pedido</label><input name="numero" inputmode="numeric" value="<?=h($numero)?>" placeholder="Ex.: 125"></div><div><label>Cliente</label><input name="cliente" value="<?=h($cliente)?>" placeholder="Nome"></div><div><label>Status</label><select name="status"><option value="">Todos</option><?php foreach($statuses as $k=>$v):?><option value="<?=h($k)?>" <?=$status===$k?'selected':''?>><?=h($v)?></option><?php endforeach;?></select></div></div><button>Consultar pedidos</button> <a href="pedidos.php">Limpar</a></form><div class="table-wrap"><table><thead><tr><th>Nº</th><th>Cliente</th><th>Serviço</th><th>Quantidade</th><th>Pedido</th><th>Prazo</th><th>Valor</th><th>Responsável</th><th>Status</th><th>Atualizar</th></tr></thead><tbody><?php foreach($pedidos as $p):?><tr><td>#<?=$p['id']?></td><td><?=h($p['cliente_nome'])?></td><td><?=h($p['servico'])?><br><small><?=h($p['descricao_material'])?></small></td><td><?=h($p['quantidade'])?></td><td><?=date('d/m/Y',strtotime($p['data_pedido']))?></td><td><?=date('d/m/Y',strtotime($p['prazo']))?></td><td>R$ <?=number_format($p['valor'],2,',','.')?></td><td><?=h($p['responsavel'])?></td><td><?=h($statuses[$p['status']]??$p['status'])?></td><td><form method="post" class="inline"><input type="hidden" name="id" value="<?=$p['id']?>"><select name="status"><?php foreach($statuses as $k=>$v):?><option value="<?=h($k)?>" <?=$p['status']===$k?'selected':''?>><?=h($v)?></option><?php endforeach;?></select><button class="mini">Salvar</button></form></td></tr><?php endforeach;?><?php if(!$pedidos):?><tr><td colspan="10">Nenhum pedido encontrado.</td></tr><?php endif;?></tbody></table></div></section></main></body></html>

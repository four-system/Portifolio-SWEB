<?php
session_start(); require_once 'config/conexao.php'; require_once 'config/auth.php'; exigir_login();
$clientes=$pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$funcionarios=$pdo->query("SELECT id,nome FROM usuarios WHERE ativo=1 ORDER BY nome")->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $cliente=(int)($_POST['cliente_id']??0); $servico=trim($_POST['servico']??''); $material=trim($_POST['descricao_material']??'');
    $qtd=(float)($_POST['quantidade']??0); $data=$_POST['data_pedido']??date('Y-m-d'); $prazo=$_POST['prazo']??'';
    $valor=(float)str_replace(',','.',$_POST['valor']??'0'); $responsavel=(int)($_POST['funcionario_id']??0);
    $q=$pdo->prepare('SELECT id FROM clientes WHERE id=?'); $q->execute([$cliente]); $clienteOk=$q->fetch();
    $q=$pdo->prepare('SELECT id FROM usuarios WHERE id=? AND ativo=1'); $q->execute([$responsavel]); $funcOk=$q->fetch();
    if(!$clienteOk || !$funcOk || $servico==='' || $material==='' || $qtd<=0 || $valor<0 || !$prazo || !$data){
        flash('Confira os campos: cliente, serviço, material, quantidade, datas, valor e responsável.');
    } elseif($prazo<$data){ flash('O prazo combinado não pode ser anterior à data do pedido.'); }
    else {
        $q=$pdo->prepare("INSERT INTO pedidos(cliente_id,servico,descricao_material,quantidade,data_pedido,prazo,valor,funcionario_id,status) VALUES(?,?,?,?,?,?,?,?, 'recebido')");
        $q->execute([$cliente,$servico,$material,$qtd,$data,$prazo,$valor,$responsavel]);
        flash('Pedido #'.$pdo->lastInsertId().' registrado com sucesso.');
        header('Location: pedidos.php'); exit;
    }
}
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Novo pedido</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Registrar pedido gráfico</h2><a class="link-sair" href="pedidos.php">← Pedidos</a></header><section class="content"><?php mostrar_flash();?><form class="form-box" method="post"><label>Cliente *</label><select name="cliente_id" required><option value="">Selecione um cliente</option><?php foreach($clientes as $c):?><option value="<?=$c['id']?>" <?=((int)($_POST['cliente_id']??0)===$c['id'])?'selected':''?>><?=h($c['nome'])?></option><?php endforeach;?></select><?php if(!$clientes):?><p class="alerta">Cadastre um cliente antes de registrar pedidos. <a href="clientes.php">Cadastrar cliente</a></p><?php endif;?><label>Serviço solicitado *</label><input name="servico" required maxlength="150" value="<?=h($_POST['servico']??'')?>" placeholder="Ex.: Cartões de visita, banners, folders"><label>Descrição do material *</label><textarea name="descricao_material" required><?=h($_POST['descricao_material']??'')?></textarea><div class="form-row"><div><label>Quantidade *</label><input type="number" name="quantidade" min="0.01" step="0.01" required value="<?=h($_POST['quantidade']??'1')?>"></div><div><label>Data do pedido *</label><input type="date" name="data_pedido" required value="<?=h($_POST['data_pedido']??date('Y-m-d'))?>"></div><div><label>Prazo combinado *</label><input type="date" name="prazo" required value="<?=h($_POST['prazo']??'')?>"></div></div><div class="form-row"><div><label>Valor (R$) *</label><input type="number" name="valor" min="0" step="0.01" required value="<?=h($_POST['valor']??'0.00')?>"></div><div><label>Funcionário responsável *</label><select name="funcionario_id" required><option value="">Selecione</option><?php foreach($funcionarios as $f):?><option value="<?=$f['id']?>" <?=((int)($_POST['funcionario_id']??$_SESSION['usuario']['id'])===$f['id'])?'selected':''?>><?=h($f['nome'])?></option><?php endforeach;?></select></div></div><button <?=!$clientes||!$funcionarios?'disabled':''?>>Registrar pedido</button></form></section></main></body></html>

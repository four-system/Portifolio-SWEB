<?php
session_start(); require_once 'config/conexao.php'; require_once 'config/auth.php'; exigir_login();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $acao=$_POST['acao']??''; $id=(int)($_POST['id']??0);
    $nome=trim($_POST['nome']??''); $telefone=trim($_POST['telefone']??''); $email=trim($_POST['email']??'');
    if ($acao==='salvar') {
        if ($nome==='') flash('Informe o nome ou razão social.');
        elseif ($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL)) flash('Informe um e-mail válido.');
        else {
            if ($id) { $q=$pdo->prepare('UPDATE clientes SET nome=?,telefone=?,email=? WHERE id=?'); $q->execute([$nome,$telefone?:null,$email?:null,$id]); flash('Cliente atualizado.'); }
            else { $q=$pdo->prepare('INSERT INTO clientes(nome,telefone,email) VALUES(?,?,?)'); $q->execute([$nome,$telefone?:null,$email?:null]); flash('Cliente cadastrado.'); }
        }
    }
    header('Location: clientes.php'); exit;
}
$edit=null; if(isset($_GET['editar'])) { $q=$pdo->prepare('SELECT * FROM clientes WHERE id=?'); $q->execute([(int)$_GET['editar']]); $edit=$q->fetch(); }
$busca=trim($_GET['busca']??'');
if($busca!==''){ $q=$pdo->prepare('SELECT * FROM clientes WHERE nome LIKE ? OR telefone LIKE ? OR email LIKE ? ORDER BY nome'); $like='%'.$busca.'%'; $q->execute([$like,$like,$like]); $clientes=$q->fetchAll(); }
else $clientes=$pdo->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Clientes</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Cadastro de clientes</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash();?><h1><?= $edit?'Editar cliente':'Novo cliente'?></h1><form class="form-box" method="post"><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?=h($edit['id']??'')?>"><label>Nome ou razão social *</label><input name="nome" required maxlength="150" value="<?=h($edit['nome']??'')?>"><div class="form-row"><div><label>Telefone</label><input name="telefone" maxlength="30" value="<?=h($edit['telefone']??'')?>"></div><div><label>E-mail</label><input type="email" name="email" maxlength="150" value="<?=h($edit['email']??'')?>"></div></div><button>Salvar cliente</button><?php if($edit):?> <a class="button-link" href="clientes.php">Cancelar</a><?php endif;?></form><h2>Clientes cadastrados</h2><form method="get" class="inline"><input name="busca" placeholder="Buscar nome, telefone ou e-mail" value="<?=h($busca)?>"><button class="mini">Buscar</button><a href="clientes.php">Limpar</a></form><div class="table-wrap"><table><thead><tr><th>Nome / Razão social</th><th>Telefone</th><th>E-mail</th><th>Ação</th></tr></thead><tbody><?php foreach($clientes as $c):?><tr><td><?=h($c['nome'])?></td><td><?=h($c['telefone']?:'—')?></td><td><?=h($c['email']?:'—')?></td><td><a href="?editar=<?=$c['id']?>">Editar</a></td></tr><?php endforeach;?><?php if(!$clientes):?><tr><td colspan="4">Nenhum cliente encontrado.</td></tr><?php endif;?></tbody></table></div></section></main></body></html>

<?php
session_start();
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_cargo(['administrador']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'salvar') {
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $nivel = $_POST['nivel'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (!$nome || !$login || !in_array($nivel, ['administrador','gerente','funcionario'], true)) {
            flash('Confira os campos obrigatórios.');
        } elseif (!$id && !$senha) {
            flash('Informe uma senha para o novo usuário.');
        } else {
            try {
                // Verifica o login antes de salvar; ignora o próprio usuário durante edição.
                $check = $pdo->prepare('SELECT id, login FROM usuarios WHERE login = ? AND id <> ? LIMIT 1');
                $check->execute([$login, $id]);
                $existente = $check->fetch();
                if ($existente) {
                    flash('Já existe um usuário com esse login/e-mail: ' . $existente['login'] . '. Confira a lista ou escolha outro.');
                } elseif ($id) {
                    if ($senha !== '') {
                        $q = $pdo->prepare('UPDATE usuarios SET nome=?, login=?, nivel=?, senha=? WHERE id=?');
                        $q->execute([$nome, $login, $nivel, password_hash($senha, PASSWORD_DEFAULT), $id]);
                    } else {
                        $q = $pdo->prepare('UPDATE usuarios SET nome=?, login=?, nivel=? WHERE id=?');
                        $q->execute([$nome, $login, $nivel, $id]);
                    }
                    flash('Usuário atualizado com sucesso.');
                } else {
                    $q = $pdo->prepare('INSERT INTO usuarios(nome,login,nivel,senha) VALUES(?,?,?,?)');
                    $q->execute([$nome, $login, $nivel, password_hash($senha, PASSWORD_DEFAULT)]);
                    flash('Usuário cadastrado com sucesso.');
                }
            } catch (PDOException $e) {
                // 23000 também pode indicar outras violações de integridade; não presume login duplicado.
                flash('Não foi possível salvar. Erro do banco (' . h($e->getCode()) . '). Verifique os campos e, se persistir, envie esta mensagem ao suporte.');
            }
        }
    } elseif ($acao === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id !== (int)$_SESSION['usuario']['id']) {
            $pdo->prepare('UPDATE usuarios SET ativo=IF(ativo=1,0,1) WHERE id=?')->execute([$id]);
            flash('Status do usuário alterado.');
        } else {
            flash('Você não pode desativar seu próprio usuário.');
        }
    }
    header('Location: usuarios.php');
    exit;
}
$edit=null;if(isset($_GET['editar'])){$q=$pdo->prepare('SELECT * FROM usuarios WHERE id=?');$q->execute([(int)$_GET['editar']]);$edit=$q->fetch();}$users=$pdo->query('SELECT id,nome,login,nivel,ativo,criado_em FROM usuarios ORDER BY nome')->fetchAll(); ?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Usuários</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Gerenciar usuários</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><h1><?= $edit?'Editar usuário':'Cadastrar usuário'?></h1><?php mostrar_flash();?><form class="form-box" method="post" autocomplete="off"><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?=h($edit['id']??'')?>"><label>Nome</label><input name="nome" required value="<?=h($edit['nome']??'')?>"><label>Login/e-mail</label><input name="login" required autocomplete="off" autocapitalize="none" spellcheck="false" readonly onfocus="this.removeAttribute('readonly')" value="<?=h($edit['login']??'')?>"><label>Cargo</label><select name="nivel"><option value="funcionario" <?=($edit['nivel']??'')==='funcionario'?'selected':''?>>Funcionário</option><option value="gerente" <?=($edit['nivel']??'')==='gerente'?'selected':''?>>Gerente</option><option value="administrador" <?=($edit['nivel']??'')==='administrador'?'selected':''?>>Administrador</option></select><label><?= $edit?'Nova senha (deixe em branco para manter)':'Senha'?></label><input type="password" name="senha" <?= $edit?'':'required'?> minlength="6"><button>Salvar usuário</button><?php if($edit):?><a class="botao-secundario" href="usuarios.php">Cancelar edição</a><?php endif;?></form><h2>Usuários cadastrados</h2><div class="table-wrap"><table><thead><tr><th>Nome</th><th>Login</th><th>Cargo</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($users as $x):?><tr><td><?=h($x['nome'])?></td><td><?=h($x['login'])?></td><td><?=h($x['nivel'])?></td><td><?=$x['ativo']?'Ativo':'Desativado'?></td><td><a href="?editar=<?=$x['id']?>">Editar</a> <form class="inline" method="post"><input type="hidden" name="acao" value="toggle"><input type="hidden" name="id" value="<?=$x['id']?>"><button class="mini" <?=$x['id']==$_SESSION['usuario']['id']?'disabled':''?>><?=$x['ativo']?'Desativar':'Ativar'?></button></form></td></tr><?php endforeach;?></tbody></table></div></section></main></body></html>
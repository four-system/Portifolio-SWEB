<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_cargo(['administrador']);

// Descobre se a tabela usuarios possui a coluna "email" (instalações antigas podem não ter)
// e se ela aceita NULL. Isso evita assumir a estrutura do banco.
$colEmail = null;
try {
    $colEmail = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'email'")->fetch() ?: null;
} catch (PDOException $e) {
    error_log('usuarios.php: falha ao inspecionar coluna email: ' . $e->getMessage());
}
$temEmail = $colEmail !== null;
$emailAceitaNull = $temEmail && strtoupper((string)($colEmail['Null'] ?? 'NO')) === 'YES';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'salvar') {
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $nivel = $_POST['nivel'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Se o campo e-mail ficou vazio mas o login já é um e-mail válido, usa o login como e-mail.
        if ($temEmail && $email === '' && filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $email = $login;
        }
        // Valor realmente gravado na coluna email:
        //  - coluna aceita NULL: vazio vira NULL (vários usuários sem e-mail são permitidos pelo UNIQUE);
        //  - coluna NOT NULL (banco ainda não migrado): usa o login, que já é único, em vez de '' repetido.
        $emailGravar = null;
        if ($temEmail) {
            $emailGravar = $email !== '' ? $email : ($emailAceitaNull ? null : $login);
        }

        if (!$nome || !$login || !in_array($nivel, ['administrador','gerente','funcionario'], true)) {
            flash_erro('Confira os campos obrigatórios.');
        } elseif ($temEmail && $email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash_erro('Informe um e-mail válido ou deixe o campo em branco.');
        } elseif (!$id && !$senha) {
            flash_erro('Informe uma senha para o novo usuário.');
        } elseif ($senha !== '' && mb_strlen($senha) < 6) {
            flash_erro('A senha deve ter pelo menos 6 caracteres.');
        } elseif ($id && $id === (int)$_SESSION['usuario']['id'] && $nivel !== 'administrador') {
            flash_erro('Você não pode remover o seu próprio cargo de administrador.');
        } else {
            try {
                // 1) Duplicidade de LOGIN (ignora o próprio registro na edição).
                $check = $pdo->prepare('SELECT id FROM usuarios WHERE login = ? AND id <> ? LIMIT 1');
                $check->execute([$login, $id]);
                if ($check->fetch()) {
                    flash_erro('Este login já está em uso por outro usuário. Informe outro login ou edite o usuário existente.');
                    header('Location: usuarios.php' . ($id ? '?editar=' . $id : '')); exit;
                }
                // 2) Duplicidade de E-MAIL (somente se houver e-mail real informado).
                if ($temEmail && $emailGravar !== null && $emailGravar !== '') {
                    $check = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ? LIMIT 1');
                    $check->execute([$emailGravar, $id]);
                    if ($check->fetch()) {
                        flash_erro('Este e-mail já está em uso por outro usuário. Informe outro e-mail ou edite o usuário existente.');
                        header('Location: usuarios.php' . ($id ? '?editar=' . $id : '')); exit;
                    }
                }

                if ($id) {
                    $campos = 'nome=?, login=?, nivel=?';
                    $args = [$nome, $login, $nivel];
                    if ($temEmail) { $campos .= ', email=?'; $args[] = $emailGravar; }
                    if ($senha !== '') { $campos .= ', senha=?'; $args[] = password_hash($senha, PASSWORD_DEFAULT); }
                    $args[] = $id;
                    $pdo->prepare("UPDATE usuarios SET $campos WHERE id=?")->execute($args);
                    flash('Usuário atualizado com sucesso.');
                } else {
                    $colunas = 'nome,login,nivel,senha';
                    $marcas = '?,?,?,?';
                    $args = [$nome, $login, $nivel, password_hash($senha, PASSWORD_DEFAULT)];
                    if ($temEmail) { $colunas .= ',email'; $marcas .= ',?'; $args[] = $emailGravar; }
                    $pdo->prepare("INSERT INTO usuarios($colunas) VALUES($marcas)")->execute($args);
                    flash('Usuário cadastrado com sucesso.');
                }
            } catch (PDOException $e) {
                $codigoMysql = (int)($e->errorInfo[1] ?? 0);
                $detalhe = (string)($e->errorInfo[2] ?? $e->getMessage());
                // Detalhe técnico completo só no log do servidor (nunca na tela).
                error_log('usuarios.php: falha ao salvar usuário. SQLSTATE=' . $e->getCode()
                    . '; MySQL=' . $codigoMysql . '; detalhe=' . $detalhe);
                if ($codigoMysql === 1062) {
                    // O MySQL informa qual índice UNIQUE foi violado: "... for key 'login'" ou "... for key 'email'".
                    if (preg_match("/for key '(?:[^']*\\.)?([^']+)'/i", $detalhe, $m)) {
                        $chave = strtolower($m[1]);
                    } else { $chave = ''; }
                    if ($chave === 'login') {
                        flash_erro('Este login já está em uso por outro usuário.');
                    } elseif ($chave === 'email') {
                        flash_erro('Este e-mail já está em uso por outro usuário.');
                    } else {
                        flash_erro('Já existe um usuário com um dos dados únicos informados (login ou e-mail).');
                    }
                } elseif (in_array($codigoMysql, [1364, 1048, 1265, 1406], true)) {
                    flash_erro('O banco recusou um dos campos (obrigatório ausente, vazio ou longo demais). Confira os dados informados. Se persistir, avise o administrador do sistema.');
                } else {
                    flash_erro('Não foi possível salvar o usuário por um erro interno. Avise o administrador do sistema (ref. MySQL ' . $codigoMysql . ').');
                }
            }
        }
    } elseif ($acao === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id !== (int)$_SESSION['usuario']['id']) {
            $pdo->prepare('UPDATE usuarios SET ativo=IF(ativo=1,0,1) WHERE id=?')->execute([$id]);
            flash('Status do usuário alterado.');
        } else {
            flash_erro('Você não pode desativar seu próprio usuário.');
        }
    }
    header('Location: usuarios.php');
    exit;
}
$edit=null;if(isset($_GET['editar'])){$q=$pdo->prepare('SELECT * FROM usuarios WHERE id=?');$q->execute([(int)$_GET['editar']]);$edit=$q->fetch()?:null;}$users=$pdo->query('SELECT id,nome,login,nivel,ativo,criado_em FROM usuarios ORDER BY nome')->fetchAll(); ?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Usuários</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Gerenciar usuários</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><h1><?= $edit?'Editar usuário':'Cadastrar usuário'?></h1><?php mostrar_flash();?><form class="form-box" method="post" autocomplete="off"><?= csrf_field() ?><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?=h($edit['id']??'')?>"><label>Nome</label><input name="nome" required value="<?=h($edit['nome']??'')?>"><label>Login/e-mail</label><input name="login" required autocomplete="off" autocapitalize="none" spellcheck="false" readonly onfocus="this.removeAttribute('readonly')" value="<?=h($edit['login']??'')?>"><?php if($temEmail):?><label>E-mail (opcional)</label><input type="email" name="email" autocomplete="off" value="<?=h($edit['email']??'')?>"><?php endif;?><label>Cargo</label><select name="nivel"><option value="funcionario" <?=($edit['nivel']??'')==='funcionario'?'selected':''?>>Funcionário</option><option value="gerente" <?=($edit['nivel']??'')==='gerente'?'selected':''?>>Gerente</option><option value="administrador" <?=($edit['nivel']??'')==='administrador'?'selected':''?>>Administrador</option></select><label><?= $edit?'Nova senha (deixe em branco para manter)':'Senha'?></label><input type="password" name="senha" <?= $edit?'':'required'?> minlength="6"><button>Salvar usuário</button><?php if($edit):?><a class="botao-secundario" href="usuarios.php">Cancelar edição</a><?php endif;?></form><h2>Usuários cadastrados</h2><div class="table-wrap"><table><thead><tr><th>Nome</th><th>Login</th><th>Cargo</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($users as $x):?><tr><td><?=h($x['nome'])?></td><td><?=h($x['login'])?></td><td><?=h($x['nivel'])?></td><td><?=$x['ativo']?'Ativo':'Desativado'?></td><td><a href="?editar=<?=$x['id']?>">Editar</a> <form class="inline" method="post"><?= csrf_field() ?><input type="hidden" name="acao" value="toggle"><input type="hidden" name="id" value="<?=$x['id']?>"><button class="mini" <?=$x['id']==$_SESSION['usuario']['id']?'disabled':''?>><?=$x['ativo']?'Desativar':'Ativar'?></button></form></td></tr><?php endforeach;?></tbody></table></div></section></main></body></html>
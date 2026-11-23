<?php
/**
 * Cria o PRIMEIRO administrador de uma instalação nova (tabela usuarios vazia).
 * Depois de usar, APAGUE este arquivo. Se já existir qualquer usuário, ele se recusa a funcionar.
 */
require_once 'config/conexao.php';
require_once 'config/auth.php';
$total = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$msg = ''; $ok = false;
if ($total === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $nome = trim($_POST['nome'] ?? ''); $login = trim($_POST['login'] ?? ''); $senha = (string)($_POST['senha'] ?? '');
    if ($nome === '' || $login === '' || mb_strlen($senha) < 8) $msg = 'Preencha nome, login e uma senha com pelo menos 8 caracteres.';
    else {
        $pdo->prepare("INSERT INTO usuarios(nome,login,senha,nivel) VALUES(?,?,?,'administrador')")->execute([$nome, $login, password_hash($senha, PASSWORD_DEFAULT)]);
        $ok = true;
    }
}
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Criar administrador</title><link rel="stylesheet" href="style.css"></head><body><main class="login-container"><section class="login-card"><h1>Primeiro administrador</h1>
<?php if ($ok): ?><div class="alerta sucesso">Administrador criado. <strong>Apague o arquivo criar_admin.php</strong> e <a href="index.php">entre no sistema</a>.</div>
<?php elseif ($total > 0): ?><div class="alerta erro">Já existem usuários cadastrados. Este assistente está desativado — apague o arquivo criar_admin.php.</div>
<?php else: ?><?php if ($msg): ?><div class="alerta erro"><?= h($msg) ?></div><?php endif; ?><form method="post"><?= csrf_field() ?><label>Nome</label><input name="nome" required><label>Login</label><input name="login" required autocomplete="off"><label>Senha (mín. 8 caracteres)</label><input type="password" name="senha" required minlength="8" autocomplete="new-password"><button>Criar administrador</button></form><?php endif; ?>
</section></main></body></html>

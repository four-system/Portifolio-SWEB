<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_verificar();

// Limite simples contra tentativa de adivinhar senha: 5 erros => espera de 60 segundos (por sessão).
if (($_SESSION['login_bloqueado_ate'] ?? 0) > time()) {
    $_SESSION['erro_login'] = 'Muitas tentativas. Aguarde alguns segundos e tente de novo.';
    header('Location: index.php'); exit;
}

$login = trim($_POST['login'] ?? ''); $senha = (string)($_POST['senha'] ?? '');
$u = false;
if ($login !== '' && $senha !== '') {
    try {
        // A tela diz "Usuário ou e-mail": aceita os dois.
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE (login=? OR email=?) AND ativo=1 LIMIT 1');
        $stmt->execute([$login, $login]);
    } catch (PDOException $e) {
        // Banco antigo sem a coluna email: tenta só pelo login.
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE login=? AND ativo=1 LIMIT 1');
        $stmt->execute([$login]);
    }
    $u = $stmt->fetch();
}
if (!$u || !password_verify($senha, $u['senha'])) {
    $_SESSION['login_falhas'] = ($_SESSION['login_falhas'] ?? 0) + 1;
    if ($_SESSION['login_falhas'] >= 5) { $_SESSION['login_bloqueado_ate'] = time() + 60; $_SESSION['login_falhas'] = 0; }
    $_SESSION['erro_login'] = 'Usuário ou senha incorretos, ou acesso desativado.';
    header('Location: index.php'); exit;
}
// Atualiza o hash se o algoritmo padrão do PHP mudou.
if (password_needs_rehash($u['senha'], PASSWORD_DEFAULT)) {
    $pdo->prepare('UPDATE usuarios SET senha=? WHERE id=?')->execute([password_hash($senha, PASSWORD_DEFAULT), $u['id']]);
}
session_regenerate_id(true);
unset($_SESSION['login_falhas'], $_SESSION['login_bloqueado_ate']);
$_SESSION['usuario'] = ['id' => (int)$u['id'], 'nome' => $u['nome'], 'login' => $u['login'], 'nivel' => $u['nivel']];
header('Location: painel.php'); exit;

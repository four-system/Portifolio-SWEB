<?php
require_once 'config/auth.php';
if (isset($_SESSION['usuario'])) { header('Location: painel.php'); exit; }
$erro = $_SESSION['erro_login'] ?? ''; unset($_SESSION['erro_login']);
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | Gráfica VAV</title><link rel="stylesheet" href="style.css"></head><body><main class="login-container"><section class="login-card"><div class="logo">GV</div><p class="eyebrow">SISTEMA INTERNO</p><h1>Bem-vindo</h1><p class="subtitle">Entre com seus dados para continuar.</p><?php if ($erro): ?><div class="alerta erro"><?= h($erro) ?></div><?php endif; ?><form action="processa_login.php" method="post"><?= csrf_field() ?><label>Usuário ou e-mail</label><input name="login" required autocomplete="username"><label>Senha</label><input type="password" name="senha" required autocomplete="current-password"><button>Entrar no sistema</button></form><p class="nota">Acesso permitido somente para usuários autorizados.</p></section><footer>© <?= date('Y') ?> — Sistema de Gestão para Gráfica</footer></main></body></html>

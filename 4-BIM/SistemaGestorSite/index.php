<?php
session_start();

if (isset($_SESSION["usuario"])) {
    header("Location: painel.php");
    exit;
}

$erro = $_SESSION["erro_login"] ?? "";
unset($_SESSION["erro_login"]);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Gráfica VAV</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="login-container">
        <section class="login-card">
            <div class="logo">GV</div>
            <p class="eyebrow">SISTEMA INTERNO</p>
            <h1>Bem-vindo</h1>
            <p class="subtitle">Entre com seus dados para continuar.</p>

            <?php if ($erro): ?>
                <div class="alerta"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form action="processa_login.php" method="POST">
                <label for="login">Usuário</label>
                <input type="text" id="login" name="login"
                       placeholder="Digite seu usuário" required autocomplete="username">

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha"
                       placeholder="Digite sua senha" required autocomplete="current-password">

                <label for="nivel">Tipo de usuário</label>
                <select id="nivel" name="nivel" required>
                    <option value="" disabled selected>Selecione seu nível</option>
                    <option value="administrador">Administrador</option>
                    <option value="gerente">Gerente</option>
                    <option value="funcionario">Funcionário</option>
                </select>

                <button type="submit">Entrar no sistema</button>
            </form>

            <p class="nota">Acesso permitido somente para funcionários autorizados.</p>
        </section>
        <footer>© <?= date("Y") ?> — Sistema de Gestão para Gráfica</footer>
    </main>
</body>
</html>

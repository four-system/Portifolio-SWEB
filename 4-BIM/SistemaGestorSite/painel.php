<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION["usuario"];
$nomesNiveis = [
    "administrador" => "Administrador",
    "gerente" => "Gerente",
    "funcionario" => "Funcionário"
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Gráfica VAV</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="painel-container">
        <section class="painel-card">
            <div class="logo">GV</div>
            <p class="eyebrow">SISTEMA INTERNO</p>
            <h1>Login realizado!</h1>
            <p class="subtitle">Olá, <?= htmlspecialchars($usuario["nome"]) ?>.</p>
            <div class="dados">
                <p><strong>Usuário:</strong> <?= htmlspecialchars($usuario["login"]) ?></p>
                <p><strong>Nível de acesso:</strong>
                    <?= htmlspecialchars($nomesNiveis[$usuario["nivel"]] ?? $usuario["nivel"]) ?>
                </p>
            </div>
            <p class="nota">Esta é uma página provisória. O painel e as funções de cada nível serão desenvolvidos depois.</p>
            <a class="botao-secundario" href="logout.php">Sair</a>
        </section>
    </main>
</body>
</html>

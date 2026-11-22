<?php
session_start();
require_once "config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$senha = $_POST["senha"] ?? "";
$nivelEscolhido = $_POST["nivel"] ?? "";

$niveisPermitidos = ["administrador", "gerente", "funcionario"];

if ($login === "" || $senha === "" || !in_array($nivelEscolhido, $niveisPermitidos, true)) {
    $_SESSION["erro_login"] = "Preencha todos os campos corretamente.";
    header("Location: index.php");
    exit;
}

$sql = "SELECT id, nome, login, senha, nivel, ativo
        FROM usuarios
        WHERE login = :login
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(["login" => $login]);
$usuario = $stmt->fetch();

/*
  O nível selecionado é conferido com o nível salvo no banco.
  Assim, alguém não consegue virar administrador apenas escolhendo essa opção.
*/
if (
    !$usuario ||
    (int)$usuario["ativo"] !== 1 ||
    !password_verify($senha, $usuario["senha"]) ||
    $usuario["nivel"] !== $nivelEscolhido
) {
    $_SESSION["erro_login"] = "Usuário, senha ou tipo de acesso incorreto.";
    header("Location: index.php");
    exit;
}

session_regenerate_id(true);
$_SESSION["usuario"] = [
    "id" => $usuario["id"],
    "nome" => $usuario["nome"],
    "login" => $usuario["login"],
    "nivel" => $usuario["nivel"]
];

header("Location: painel.php");
exit;

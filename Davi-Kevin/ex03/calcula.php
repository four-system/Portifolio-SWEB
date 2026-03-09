<?php
$num1 = $_GET["num1"];
$num2 = $_GET["num2"];
$op = $_GET["op"];

if ($op == "+") {
    $res = $num1 + $num2;
} elseif ($op == "-") {
    $res = $num1 - $num2;
} elseif ($op == "*") {
    $res = $num1 * $num2;
} elseif ($op == "/") {
    $res = ($num2 != 0) ? ($num1 / $num2) : "Erro: Divisão por zero";
} else {
    $res = "Operador inválido";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado Calculadora</title>
</head>
<body>
    <h1>O resultado da operação é:</h1>
    <p><strong><?php echo $res; ?></strong></p>
    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
<?php
$n1 = $_GET["n1"];

if ($n1 >= 100 && $n1 <= 200) {
    $mensagem = "O numero $n1 está entre 100 e 200.";
} else {
    $mensagem = "O numero $n1 nao está entre 100 e 200.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
</head>
<body>
    <h1>Resultado</h1>
    <p><strong><?php echo $mensagem; ?></strong></p>
    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
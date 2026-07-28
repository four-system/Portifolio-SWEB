<?php
$valor = $_GET["valor"];
$tipo = $_GET["tipo"];

if ($tipo == "F") {

    $resultado = 5/9 * ($valor - 32);
    $mensagem = "$valor °F equivale a " . number_format($resultado, 2) . " °C";
} else {
  
    $resultado = ($valor * 9/5) + 32;
    $mensagem = "$valor °C equivale a " . number_format($resultado, 2) . " °F";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
</head>
<body>
    <h1>Resultado da Conversão</h1>
    <p><strong><?php echo $mensagem; ?></strong></p>
    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
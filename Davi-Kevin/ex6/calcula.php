<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Resultado</title>
</head>

<body>

<h2>Sequência de Fibonacci</h2>

<?php

$n = $_GET['numero'];

$a = 0;
$b = 1;

echo "Sequência:<br><br>";

for ($i = 0; $i < $n; $i++) {
    echo $a . " ";
    $proximo = $a + $b;
    $a = $b;
    $b = $proximo;
}

?>

<br><br>
<a href="index.php">Voltar</a>

</body>
</html>
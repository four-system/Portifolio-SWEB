<?php
function fatorial($n) {
    $fat = 1;
    for ($i = 1; $i <= $n; $i++) {
        $fat *= $i;
    }
    return $fat;
}

$nums = [
    $_GET["n1"],
    $_GET["n2"],
    $_GET["n3"],
    $_GET["n4"],
    $_GET["n5"]
];

$soma = 0;

foreach ($nums as $num) {
    $soma += fatorial($num);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado Fatorial</title>
</head>
<body>
    <h1>Resultado do Somatório</h1>
    <p>A soma dos fatoriais dos números digitados é: <strong><?php echo $soma; ?></strong></p>
    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
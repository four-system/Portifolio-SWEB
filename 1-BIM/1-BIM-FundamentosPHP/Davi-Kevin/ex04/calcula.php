<?php
$n = $_GET["n"];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Triângulo</title>
</head>
<body>
    <h1>Triângulo de <?php echo $n; ?> linhas:</h1>
    <div style="font-family: monospace; line-height: 1.5;">
        <?php
        for ($i = 1; $i <= $n; $i++) {
            for ($j = 1; $j <= $i; $j++) {
                echo $j . " ";
            }
            echo "<br>";
        }
        ?>
    </div>
    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
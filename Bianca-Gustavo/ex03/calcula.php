<?php

$numero1 = $_GET ["numero1"];
$numero2 = $_GET ["numero2"];
$operacao = $_GET["operacao"];

if ($operacao == "+") {
    $t = $numero1 + $numero2;
    echo "Resultado: ". $t;
}

else if ($operacao == "-") {
    $t = $numero1 - $numero2;
    echo "Resultado: ". $t;
}

else if ($operacao == "*") {
    $t = $numero1 * $numero2;
    echo "Resultado: ". $t;
}

else if ($operacao == "/") {
    $t = $numero1 / $numero2;
    echo "Resultado: ". $t;
}
?>
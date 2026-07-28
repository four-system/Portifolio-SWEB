<?php

$numeros = [
    $_GET['n1'],
    $_GET['n2'],
    $_GET['n3'],
    $_GET['n4'],
    $_GET['n5'],
    $_GET['n6'],
    $_GET['n7'],
    $_GET['n8']
];

$positivos = [];
$negativos = [];

foreach ($numeros as $num) {

    if ($num >= 0) {
        $positivos[] = $num;
    } else {
        $negativos[] = $num;
    }

}

echo "<h2>Números Positivos</h2>";

foreach ($positivos as $p) {
    echo $p . "<br>";
}

echo "<h2>Números Negativos</h2>";

foreach ($negativos as $n) {
    echo $n . "<br>";
}

?>
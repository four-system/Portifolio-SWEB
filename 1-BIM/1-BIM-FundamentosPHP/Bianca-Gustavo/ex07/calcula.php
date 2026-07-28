<?php
    $numero = $_GET["n"];
    $positivo = [];
    $negativo = [];

    foreach ($numero as $num){
        if ($num > 0){
            $positivo[] = $num;
        } else {
            $negativo[] = $num;
        }
    }
    echo "Positivos: " . implode(", ", $positivo) . "<br>";
    echo "Negativos: " . implode(", ", $negativo) . "<br>";

?>
<?php

function soma($n) {
    $resultado = 0;
    $numeros = [];

    for ($i = 1; $i <= $n; $i++) {
        $resultado += $i;
        $numeros[] = $i;
    }

    return [
        'resultado' => $resultado,
        'numeros' => $numeros
    ];
}

if (isset($_GET['numero']) && ($_GET['numero'])) {
    $numero = (int)$_GET['numero'];
    $soma = soma($numero);

    $numerosSomados = implode('+', $soma['numeros']);
    echo "$numerosSomados = {$soma['resultado']}<br>";
    echo "A soma de 0 até $numero é igual a: {$soma['resultado']}";
} else {
    echo "Por favor, forneça um número válido na URL, por exemplo: ?numero=10";
}

?>
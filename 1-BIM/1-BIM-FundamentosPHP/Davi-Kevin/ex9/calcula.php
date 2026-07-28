<?php

function calcularMedia($numeros) {
    $soma = array_sum($numeros);
    $quantidade = count($numeros);

    if ($quantidade === 0) {
        return 0; // Evita divisão por zero
    }

    return $soma / $quantidade;
}

if (isset($_GET['numeros'])) {
    $numeros = explode(',', $_GET['numeros']); // Divide os números passados como string
    $numeros = array_map('floatval', $numeros); // Converte os valores para números

    if (count($numeros) > 1) {
        $media = calcularMedia($numeros);

        echo "Os números fornecidos são: " . implode(', ', $numeros) . "<br>";
        echo "A média aritmética é: " . $media;
    } else {
        echo "Por favor, forneça mais de um número separado por vírgulas, por exemplo: ?numeros=1,2,3,4";
    }
} else {
    echo "Por favor, forneça os números na URL no formato: ?numeros=1,2,3,4";
}

?>
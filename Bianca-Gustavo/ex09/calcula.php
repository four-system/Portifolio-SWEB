<?php

function media($v) {
    $soma = 0;
    $q = count($v);

    if ($q == 0) {
        return 0;
    }

    foreach ($v as $numero) {
        $soma += $numero;
    }

    return $soma / $q;
}

if (!isset($_POST["valores"])) {
    echo "Nenhum valor foi enviado";
    exit;
}

$e = $_POST["valores"];

$v = explode(",", $e);
foreach ($v as &$numero) {
    $numero = trim($numero);
}
$r = media($v);

echo "A média é: " . $r;
?>
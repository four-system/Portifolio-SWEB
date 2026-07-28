<?php

function verificarAnoBissexto($ano) {
    if (($ano % 400 == 0) || ($ano % 4 == 0 && $ano % 100 != 0)) {
        return true;
    }
    return false;
}

if (isset($_GET['ano']) && is_numeric($_GET['ano'])) {
    $ano = (int)$_GET['ano'];

    if (verificarAnoBissexto($ano)) {
        echo "O ano $ano é bissexto.";
    } else {
        echo "O ano $ano não é bissexto.";
    }
} else {
    echo "Por favor, forneça um ano válido na URL, por exemplo: ?ano=2024";
}

?>
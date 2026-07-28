<?php

$F = isset($_GET["F"]) ? $_GET["F"] : "";
$C = isset($_GET["C"]) ? $_GET["C"] : "";

if($F !== ""){
    $p = $F - 32;
    $t = 5/9 * $p;
    
    echo $F . "ºF = " . $t . " ºC";
}

else if($C !== ""){

    $p = $C * 9/5;
    $t = $p + 32;
    echo $C . "ºC = " . $t . " ºF";
}

?>
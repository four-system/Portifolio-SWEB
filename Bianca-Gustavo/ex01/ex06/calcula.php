<?php
$n = $_GET["n"];
$i = 1;
$m = 0;
$p = 1;
for ($i = 1; $i <=$n; $i++){
    echo $m . " ";
    $proximo = $m + $p;
    $m = $p;
    $p = $proximo;
}

?>
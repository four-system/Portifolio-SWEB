<?php
$n = $_GET['n'];
$soma = 0;
for ($i = 0; $i <= $n; $i++) {
    $soma += $i;
}
echo "A somatória dos números de 0 a $n é: $soma";
?>
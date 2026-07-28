<?php

$a = $_GET["a"];
$b = $_GET["b"];
$c = $_GET["c"];
$d = $_GET["d"];
$e = $_GET["e"];


$fA = 1;
for ($i = $a; $i >= 1; $i--) {
    $fA = $fA * $i;
}


$fB = 1;
for ($i = $b; $i >= 1; $i--) {
    $fB = $fB * $i;
}


$fC = 1;
for ($i = $c; $i >= 1; $i--) {
    $fC = $fC * $i;
}


$fD = 1;
for ($i = $d; $i >= 1; $i--) {
    $fD = $fD * $i;
}


$fE = 1;
for ($i = $e; $i >= 1; $i--) {
    $fE = $fE * $i;
}


$t = $fA + $fB + $fC + $fD + $fE;


echo $a . "! = " . $fA . "<br>";
echo $b . "! = " . $fB . "<br>";
echo $c . "! = " . $fC . "<br>";
echo $d . "! = " . $fD . "<br>";
echo $e . "! = " . $fE . "<br>";

echo "<br>Total dos fatoriais: " . $t;

?>
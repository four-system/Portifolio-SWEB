<?php

$numero = $_GET["numero"];

if ($numero >=100 && $numero <=200){
    echo"O número está entre 100 e 200.";
}else{
    echo"O número não está entre 100 e 200.";
}

?>
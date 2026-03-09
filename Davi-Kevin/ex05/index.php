<!--
Data: 08/03/2026
Autor: Davi Bressan e Kevin Nozaki
Objetivo:
fazer um programa que leia 5 números inteiros e mostre a soma dos fatoriais desses números.

Exercício 5 - Somatório de Fatoriais
Leia 5 números inteiros e mostre a soma dos fatoriais desses números.
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 05</title>
</head>
<body>
    <h1>Somatório de Fatoriais</h1>
    <p>Digite 5 números inteiros:</p>
    <form action="calcula.php" method="get">
        <input type="number" name="n1" placeholder="Número 1" required><br>
        <input type="number" name="n2" placeholder="Número 2" required><br>
        <input type="number" name="n3" placeholder="Número 3" required><br>
        <input type="number" name="n4" placeholder="Número 4" required><br>
        <input type="number" name="n5" placeholder="Número 5" required><br>
        <br>
        <input type="submit" value="Calcular Soma">
    </form>

</body>
</html>
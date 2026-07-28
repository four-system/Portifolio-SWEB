<!--
Data: 08/03/2026
Autor: Davi Bressan e Kevin Nozaki
Objetivo:
Fazer um programa que leia um número n e imprima n linhas

Exercício 4 -  Triângulo Numérico
Leia um número n e imprima n linhas no seguinte formato (exemplo para n = 6):

1
1 2
1 2 3
1 2 3 4
1 2 3 4 5
1 2 3 4 5 6
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 04</title>
</head>
<body>
    <h1>Gerador de Triângulo Numérico</h1>
    <form action="calcula.php" method="get">
        <label>Digite a quantidade de linhas (n):</label>
        <input type="number" name="n" min="1" required>
        <br><br>
        <input type="submit" value="Gerar Triângulo">
    </form>
</body>
</html>
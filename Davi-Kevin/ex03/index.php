<!--
Data: 08/03/2026
Autor: Davi Bressan e Kevin Nozaki
Objetivo:
fazer um programa que leia dois números e um operador ("+", "-", "*" ou "/").

Exercício 3 - Calculadora Aritmética
Faça um programa que leia dois números e um operador ("+", "-", "*" ou "/").
O programa deve mostrar o resultado da operação.
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 03</title>
</head>
<body>
    <h1>Calculadora Aritmética</h1>
    <form action="calcula.php" method="get">
        <input type="number" name="num1" placeholder="Primeiro número" step="any" required>
        <br><br>
        <input type="number" name="num2" placeholder="Segundo número" step="any" required>
        <br><br>
        <label>Operador:</label>
        <select name="op">
            <option value="+">+</option>
            <option value="-">-</option>
            <option value="*">*</option>
            <option value="/">/</option>
        </select>
        <br><br>
        <input type="submit" value="Calcular">
    </form>

</body>
</html>
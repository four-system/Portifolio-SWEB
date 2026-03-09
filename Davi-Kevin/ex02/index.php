<!--
Data: 08/03/2026
Autor: Davi Bressan e Kevin Nozaki
Objetivo:
fazer um programa que leia um caractere "F" ou "C", indicando se o valor informado está em Fahrenheit ou Celsius.

Exercício 2 - Conversão de Temperatura
Faça um programa que leia um caractere "F" ou "C", indicando se o valor informado está em Fahrenheit ou Celsius.
Depois, o programa deve converter para a outra unidade.

Fórmula: C = 5/9 × (F − 32)
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercício 02</title>
</head>
<body>
    
<h1>Conversor de Temperatura</h1>
    <form action="calcula.php" method="get">
        <label>Temperatura:</label>
        <input type="number" name="valor" step="any" required>
        
        <br><br>
        
        <label>Converter de:</label>
        <select name="tipo">
            <option value="C">Celsius para Fahrenheit</option>
            <option value="F">Fahrenheit para Celsius</option>
        </select>
        
        <br><br>
        <input type="submit" value="Converter">
    </form>

</body>
</html>
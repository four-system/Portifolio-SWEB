<!--
Data: 09/03/2026
Autor:Kevin Nozaki e Davi Bressan
Objetivo:- Ano Bissexto
Leia um ano e informe se ele é bissexto.

Um ano é bissexto se:

É múltiplo de 400
ou
É múltiplo de 4 e não é múltiplo de 100

Exercício 10 - Ano Bissexto
Leia um ano e informe se ele é bissexto.

Um ano é bissexto se:

É múltiplo de 400
ou
É múltiplo de 4 e não é múltiplo de 100
-->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>descubra o ano bissexto</title>
</head>
<body>

<div class="card">

    <h2>digite um número</h2>

    <form action="calcula.php" method="GET">
        <input type="number" name="ano" placeholder="Digite um ano" required>

        <button type="submit">Verificar</button>
    </form>

</div>

</body>
</html>
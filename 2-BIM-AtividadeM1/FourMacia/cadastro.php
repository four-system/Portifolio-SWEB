<?php

require_once 'config/conexao.php';
require_once 'includes/header.php';

?>

<h2>Cadastrar Produto</h2>

<form method="POST">

    <input type="text" name="nome" placeholder="Nome">

    <input type="text" name="fabricante" placeholder="Fabricante">

    <input type="number" step="0.01" name="preco" placeholder="Preço">

    <input type="number" name="estoque" placeholder="Estoque">

    <button type="submit">
        Cadastrar
    </button>

</form>

<?php

require_once 'includes/footer.php';

?>
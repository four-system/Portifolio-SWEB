<?php

require_once 'config/conexao.php';
require_once 'includes/header.php';

?>

<h2>Editar Produto</h2>

<form method="POST">

    <input type="text" name="nome">

    <input type="text" name="fabricante">

    <input type="number" step="0.01" name="preco">

    <input type="number" name="estoque">

    <button type="submit">
        Salvar
    </button>

</form>

<?php

require_once 'includes/footer.php';

?>
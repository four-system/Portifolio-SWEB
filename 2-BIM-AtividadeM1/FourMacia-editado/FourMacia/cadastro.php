<?php

require_once 'config/conexao.php';
require_once 'includes/header.php';

?>

<h2>Cadastrar Produto</h2>

<?php
// processa submissão
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = trim($_POST['nome'] ?? '');
    $fab = trim($_POST['fabricante'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $estoque = $_POST['estoque'] ?? '';

    $errors = [];
    if($nome === '') $errors[] = 'Nome é obrigatório.';
    if($fab === '') $errors[] = 'Fabricante é obrigatório.';
    if($preco === '' || !is_numeric($preco)) $errors[] = 'Preço inválido.';
    if($estoque === '' || !is_numeric($estoque)) $errors[] = 'Estoque inválido.';

    if(!$errors){
        $stmt = $pdo->prepare('INSERT INTO produtos (nome,fabricante,preco,estoque) VALUES (:n,:f,:p,:e)');
        $stmt->execute([':n'=>$nome,':f'=>$fab,':p'=>$preco,':e'=>$estoque]);
        header('Location: index.php');
        exit;
    }
}

?>

<?php if(!empty($errors)): ?>
    <div class="errors">
        <ul>
            <?php foreach($errors as $err): ?>
                <li><?php echo htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">

    <input type="text" name="nome" placeholder="Nome" value="<?php echo isset($nome)?htmlspecialchars($nome):''; ?>">

    <input type="text" name="fabricante" placeholder="Fabricante" value="<?php echo isset($fab)?htmlspecialchars($fab):''; ?>">

    <input type="number" step="0.01" name="preco" placeholder="Preço" value="<?php echo isset($preco)?htmlspecialchars($preco):''; ?>">

    <input type="number" name="estoque" placeholder="Estoque" value="<?php echo isset($estoque)?htmlspecialchars($estoque):''; ?>">

    <button type="submit">
        Cadastrar
    </button>

</form>

<?php

require_once 'includes/footer.php';

?>
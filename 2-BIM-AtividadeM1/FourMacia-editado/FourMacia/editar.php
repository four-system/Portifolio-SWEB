<?php

require_once 'config/conexao.php';
require_once 'includes/header.php';

// obter id
$id = isset($_GET['id'])? (int)$_GET['id'] : 0;
if($id <= 0){
    header('Location: index.php');
    exit;
}

// carregar produto
$stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
$stmt->execute([':id'=>$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$produto){
    echo '<p>Produto não encontrado.</p>';
    require_once 'includes/footer.php';
    exit;
}

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = trim($_POST['nome'] ?? '');
    $fab = trim($_POST['fabricante'] ?? '');
    $preco = $_POST['preco'] ?? '';
    $estoque = $_POST['estoque'] ?? '';

    if($nome === '') $errors[] = 'Nome é obrigatório.';
    if($fab === '') $errors[] = 'Fabricante é obrigatório.';
    if($preco === '' || !is_numeric($preco)) $errors[] = 'Preço inválido.';
    if($estoque === '' || !is_numeric($estoque)) $errors[] = 'Estoque inválido.';

    if(!$errors){
        $up = $pdo->prepare('UPDATE produtos SET nome=:n,fabricante=:f,preco=:p,estoque=:e WHERE id=:id');
        $up->execute([':n'=>$nome,':f'=>$fab,':p'=>$preco,':e'=>$estoque,':id'=>$id]);
        header('Location: index.php');
        exit;
    }
} else {
    $nome = $produto['nome'];
    $fab = $produto['fabricante'];
    $preco = $produto['preco'];
    $estoque = $produto['estoque'];
}

?>

<h2>Editar Produto</h2>

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

    <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>">

    <input type="text" name="fabricante" value="<?php echo htmlspecialchars($fab); ?>">

    <input type="number" step="0.01" name="preco" value="<?php echo htmlspecialchars($preco); ?>">

    <input type="number" name="estoque" value="<?php echo htmlspecialchars($estoque); ?>">

    <button type="submit">
        Salvar
    </button>

</form>

<?php

require_once 'includes/footer.php';

?>
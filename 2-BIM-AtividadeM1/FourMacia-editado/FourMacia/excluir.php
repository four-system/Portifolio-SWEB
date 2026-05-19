<?php

require_once 'config/conexao.php';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id > 0) {

    $del = $pdo->prepare(
        'DELETE FROM produtos WHERE id = :id'
    );

    $del->execute([
        ':id' => $id
    ]);
}

header('Location: index.php');

exit;

?>

<?php
session_start(); require_once 'config/conexao.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
$login=trim($_POST['login']??''); $senha=$_POST['senha']??'';
$stmt=$pdo->prepare('SELECT * FROM usuarios WHERE login=? AND ativo=1 LIMIT 1'); $stmt->execute([$login]); $u=$stmt->fetch();
if (!$u || !password_verify($senha,$u['senha'])) { $_SESSION['erro_login']='Usuário ou senha incorretos, ou acesso desativado.'; header('Location: index.php'); exit; }
session_regenerate_id(true); $_SESSION['usuario']=['id'=>$u['id'],'nome'=>$u['nome'],'login'=>$u['login'],'nivel'=>$u['nivel']];
header('Location: painel.php'); exit;

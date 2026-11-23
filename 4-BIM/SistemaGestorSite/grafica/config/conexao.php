<?php
$host = "localhost";
$banco = "grafica_db";
$usuario = "root";
$senha = ""; // Padrão do XAMPP. Altere se você configurou uma senha no MySQL.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log('Conexão: ' . $e->getMessage());
    die("Não foi possível conectar ao banco de dados. Verifique se o MySQL está ligado no XAMPP e confira o arquivo config/conexao.php.");
}

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Farmácia VAV</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <div class="top">
        <button id="navToggle" aria-label="Abrir menu">☰</button>
        <div class="brand">
            <h1>Farmácia VAV</h1>
            <p class="tag">Saúde e cuidado perto de você</p>
        </div>
        <a class="cta" href="cadastro.php">+ Novo</a>
    </div>

    <nav id="mainNav">
        <form class="search" action="index.php" method="GET">
            <input type="search" name="q" placeholder="Buscar por nome ou fabricante" value="<?php echo isset($_GET['q'])?htmlspecialchars($_GET['q']):''; ?>">
            <button type="submit">Buscar</button>
        </form>
        <a href="index.php">Início</a>
        <a href="cadastro.php">Cadastro</a>
    </nav>

</header>

<main>
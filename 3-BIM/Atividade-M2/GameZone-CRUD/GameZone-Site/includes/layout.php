<?php
declare(strict_types=1);

require_once __DIR__ . '/data.php';

function render_header(string $title, string $description): void
{
    $current = page_name();
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header" id="topo">
    <nav class="navbar" aria-label="Navegação principal">
      <a class="brand" href="index.php" aria-label="GameZone - início">
        <img src="assets/gamezone-logo.svg" alt="" class="brand-logo">
        <span>GameZone</span>
      </a>

      <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <ul class="nav-links" id="menu">
        <?php
        $links = [
            'index.php' => 'Home',
            'jogos.php' => 'Jogos',
            'noticias.php' => 'Notícias',
            'lancamentos.php' => 'Lançamentos',
            'sobre.php' => 'Sobre',
            'contato.php' => 'Contato',
        ];

        foreach ($links as $href => $label) {
            $active = $current === $href ? ' class="current-page"' : '';
            echo '<li><a href="' . e($href) . '" data-page="' . e($href) . '"' . $active . '>' . e($label) . '</a></li>';
        }
        ?>
      </ul>
    </nav>
  </header>
    <?php
}

function render_footer(): void
{
    ?>
  <footer class="footer">
    <p>GameZone Entertainment - Projeto escolar com página pública e CRUD administrativo.</p>
    <a href="admin/index.php">Área administrativa</a>
    <a href="#topo">Voltar ao topo</a>
  </footer>

  <script src="script.js"></script>
</body>
</html>
    <?php
}
?>

<?php
require_once __DIR__ . '/includes/layout.php';

$jogos = load_items('jogos');
render_header('Jogos | GameZone', 'Reviews e categorias de jogos no GameZone.');
?>
  <main>
    <section class="section page-hero">
      <p class="eyebrow">Reviews e recomendações</p>
      <h1>Jogos em destaque</h1>
      <p>Cards por categoria para o usuário encontrar rápido o tipo de jogo que mais combina com ele.</p>
    </section>

    <section class="section games">
      <div class="filter-bar" aria-label="Filtros de jogos">
        <button class="filter-btn active" type="button" data-filter="todos">Todos</button>
        <button class="filter-btn" type="button" data-filter="fps">FPS</button>
        <button class="filter-btn" type="button" data-filter="rpg">RPG</button>
        <button class="filter-btn" type="button" data-filter="esports">E-sports</button>
      </div>

      <div class="card-grid">
        <?php foreach ($jogos as $index => $jogo): ?>
          <article class="game-card" data-category="<?= e($jogo['categoria']) ?>">
            <div class="game-art game-art-<?= ($index % 3) + 1 ?>"><span><?= e(strtoupper($jogo['categoria'])) ?></span></div>
            <div class="game-content">
              <p class="tag">Nota <?= e($jogo['nota']) ?></p>
              <h2><?= e($jogo['titulo']) ?></h2>
              <p><?= e($jogo['resumo']) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
<?php render_footer(); ?>

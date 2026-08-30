<?php
require_once __DIR__ . '/includes/layout.php';

$noticias = array_slice(sort_by_date_desc(load_items('noticias')), 0, 2);
$lancamentos = array_slice(load_items('lancamentos'), 0, 3);
$jogos = array_slice(load_items('jogos'), 0, 3);

render_header('GameZone | Portal Gamer', 'Página inicial pública do GameZone para clientes.');
?>
  <main>
    <section class="hero section">
      <div class="hero-content">
        <p class="eyebrow">Portal gamer mobile first</p>
        <h1>GameZone</h1>
        <p class="hero-text">
          Notícias reais, próximos lançamentos, reviews e dicas em um site rápido, organizado e pensado primeiro para celulares.
        </p>

        <div class="hero-actions">
          <a href="noticias.php" class="btn btn-primary">Ver notícias</a>
          <a href="lancamentos.php" class="btn btn-secondary">Próximos lançamentos</a>
        </div>
      </div>

      <div class="hero-panel" aria-label="Destaques do GameZone">
        <div class="console-card">
          <div class="console-screen">
            <span class="live-dot"></span>
            <p>Destaque do dia</p>
            <strong><?= e($lancamentos[0]['titulo'] ?? 'GameZone') ?></strong>
          </div>
          <div class="controller">
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    </section>

    <section class="section stats" aria-label="Resumo do site">
      <article><strong><?= count(load_items('noticias')) ?></strong><span>notícias cadastradas</span></article>
      <article><strong><?= count(load_items('lancamentos')) ?></strong><span>lançamentos cadastrados</span></article>
      <article><strong><?= count(load_items('jogos')) ?></strong><span>jogos cadastrados</span></article>
    </section>

    <section class="section page-links">
      <div class="section-heading">
        <p class="eyebrow">Conteúdo para o cliente</p>
        <h2>Novidades do portal</h2>
      </div>

      <div class="card-grid">
        <?php foreach ($noticias as $noticia): ?>
          <article class="article-card">
            <div class="article-meta">
              <span><?= e($noticia['categoria']) ?></span>
              <time datetime="<?= e($noticia['data']) ?>"><?= date('d/m/Y', strtotime($noticia['data'])) ?></time>
            </div>
            <h3><?= e($noticia['titulo']) ?></h3>
            <p><?= e($noticia['resumo']) ?></p>
            <a href="noticias.php">Ler notícia</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section games">
      <div class="section-heading">
        <p class="eyebrow">Reviews</p>
        <h2>Jogos em destaque</h2>
      </div>

      <div class="card-grid">
        <?php foreach ($jogos as $index => $jogo): ?>
          <article class="game-card" data-category="<?= e($jogo['categoria']) ?>">
            <div class="game-art game-art-<?= ($index % 4) + 1 ?>"><span><?= e(strtoupper($jogo['categoria'])) ?></span></div>
            <div class="game-content">
              <p class="tag">Nota <?= e($jogo['nota']) ?></p>
              <h3><?= e($jogo['titulo']) ?></h3>
              <p><?= e($jogo['resumo']) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
<?php render_footer(); ?>

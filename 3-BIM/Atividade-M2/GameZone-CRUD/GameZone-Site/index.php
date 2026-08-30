<?php
require_once __DIR__ . '/includes/layout.php';

$noticias = array_slice(sort_by_date_desc(load_items('noticias')), 0, 2);

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
              <time datetime="<?= e($noticia['date']) ?>"><?= date('d/m/Y', strtotime($noticia['date'])) ?></time>
            </div>
            <h3><?= e($noticia['titulo']) ?></h3>
            <p><?= e($noticia['resumo']) ?></p>
            <a href="noticias.php">Ler notícia</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
<?php render_footer(); ?>

<?php
require_once __DIR__ . '/includes/layout.php';

$noticias = sort_by_date_desc(load_items('noticias'));
render_header('Notícias | GameZone', 'Notícias recentes do mundo dos jogos no GameZone.');
?>
  <main>
    <section class="section page-hero">
      <p class="eyebrow">Atualizado pelo CRUD</p>
      <h1>Notícias de jogos</h1>
      <p>Resumo organizado de novidades recentes para o público gamer acompanhar rápido pelo celular.</p>
    </section>

    <section class="section article-list">
      <?php foreach ($noticias as $index => $noticia): ?>
        <article class="article-card <?= $index === 1 ? 'featured-card' : '' ?>">
          <div class="article-meta">
            <span><?= e($noticia['categoria']) ?></span>
            <time datetime="<?= e($noticia['data']) ?>"><?= date('d/m/Y', strtotime($noticia['data'])) ?></time>
          </div>
          <h2><?= e($noticia['titulo']) ?></h2>
          <p><?= e($noticia['resumo']) ?></p>
          <a href="<?= e($noticia['link']) ?>" target="_blank" rel="noreferrer">Fonte: <?= e($noticia['fonte']) ?></a>
        </article>
      <?php endforeach; ?>
    </section>
  </main>
<?php render_footer(); ?>

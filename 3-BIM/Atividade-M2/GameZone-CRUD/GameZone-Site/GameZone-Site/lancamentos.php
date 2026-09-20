<?php
require_once __DIR__ . '/includes/layout.php';

$lancamentos = load_items('lancamentos');
usort($lancamentos, fn ($a, $b) => strcmp((string) $a['data'], (string) $b['data']));
render_header('Lançamentos | GameZone', 'Calendário de próximos lançamentos de jogos no GameZone.');
?>
  <main>
    <section class="section page-hero">
      <p class="eyebrow">Calendário gamer</p>
      <h1>Próximos lançamentos</h1>
      <p>Jogos com data marcada e cadastro editável pela área administrativa.</p>
    </section>

    <section class="section release-timeline">
      <?php foreach ($lancamentos as $lancamento): ?>
        <article class="release-card">
          <time datetime="<?= e($lancamento['data']) ?>"><?= date('d/m/Y', strtotime($lancamento['data'])) ?></time>
          <div>
            <h2><?= e($lancamento['titulo']) ?></h2>
            <p><strong>Categoria:</strong> <?= e($lancamento['categoria']) ?></p>
            <p><strong>Plataformas:</strong> <?= e($lancamento['plataformas']) ?></p>
            <p><?= e($lancamento['resumo']) ?></p>
            <a href="<?= e($lancamento['link']) ?>" target="_blank" rel="noreferrer">Fonte: <?= e($lancamento['fonte']) ?></a>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
  </main>
<?php render_footer(); ?>

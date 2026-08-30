<?php
require_once __DIR__ . '/include/layout.php';

render_header('Sobre | GameZone', 'Proposta, público-alvo e identidade visual do GameZone.');
?>
  <main>
    <section class="section page-hero">
      <p class="eyebrow">Planejamento do projeto</p>
      <h1>Sobre o GameZone</h1>
      <p>Informações da proposta, público-alvo, mobile first e identidade visual do site.</p>
    </section>

    <section class="section split">
      <div>
        <p class="eyebrow">Proposta</p>
        <h2>Objetivo do site</h2>
        <p>O GameZone é um portal gamer moderno onde os usuários podem acompanhar novidades, descobrir jogos e acessar conteúdos úteis sobre games.</p>
      </div>
      <div class="info-grid">
        <article class="info-card"><h3>Problema resolvido</h3><p>Evitar excesso de anúncios e informações confusas, oferecendo conteúdo organizado e rápido.</p></article>
        <article class="info-card"><h3>Público-alvo</h3><p>Adolescentes e jovens gamers de 13 a 25 anos interessados em jogos online, FPS, RPG, e-sports, tecnologia e streaming.</p></article>
        <article class="info-card"><h3>Mobile first</h3><p>O site foi pensado primeiro para celular, com menu responsivo, imagens leves, textos curtos e botões grandes.</p></article>
        <article class="info-card"><h3>CRUD administrativo</h3><p>A equipe pode cadastrar, listar, editar e excluir notícias, jogos e lançamentos pela área administrativa.</p></article>
      </div>
    </section>

    <section class="section identity">
      <div>
        <p class="eyebrow">Identidade visual</p>
        <h2>Preto, roxo e azul neon</h2>
        <p>A marca usa fontes modernas, brilho neon e uma logo com controle gamer para combinar com jogos, tecnologia e entretenimento.</p>
      </div>
      <div class="identity-card">
        <img src="assets/gamezone-logo.svg" alt="Logo do GameZone com controle gamer neon">
        <div>
          <h3>GameZone Entertainment</h3>
          <p>(19) 99999-9999</p>
          <p>contato@gamezone.com</p>
          <p>Rua dos Gamers, 777 - São Paulo/SP</p>
        </div>
      </div>
    </section>
  </main>
<?php render_footer(); ?>

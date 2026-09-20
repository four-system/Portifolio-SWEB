<?php
require_once __DIR__ . '/includes/layout.php';

render_header('Contato | GameZone', 'Contato fictício do GameZone.');
?>
  <main>
    <section class="section page-hero">
      <p class="eyebrow">Contato</p>
      <h1>Fale com o GameZone</h1>
      <p>Envie sugestões de jogos, notícias ou ideias de conteúdo para o portal.</p>
    </section>

    <section class="section contact">
      <div class="contact-info">
        <h2>Dados fictícios</h2>
        <p>GameZone Entertainment</p>
        <ul>
          <li>Telefone: (19) 99999-9999</li>
          <li>E-mail: contato@gamezone.com</li>
          <li>Endereço: Rua dos Gamers, 777 - São Paulo/SP</li>
          <li>Instagram: @gamezone</li>
          <li>TikTok: @gamezone</li>
          <li>YouTube: GameZone Oficial</li>
        </ul>
      </div>

      <form class="contact-form" id="contactForm">
        <label for="name">Nome</label>
        <input id="name" name="name" type="text" placeholder="Seu nome" required>
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" placeholder="seuemail@exemplo.com" required>
        <label for="message">Mensagem</label>
        <textarea id="message" name="message" rows="4" placeholder="Digite sua mensagem" required></textarea>
        <button class="btn btn-primary" type="submit">Enviar mensagem</button>
        <p class="form-feedback" id="formFeedback" role="status"></p>
      </form>
    </section>
  </main>
<?php render_footer(); ?>

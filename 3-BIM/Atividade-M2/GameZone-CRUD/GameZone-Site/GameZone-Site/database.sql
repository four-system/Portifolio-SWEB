CREATE DATABASE IF NOT EXISTS gamezone_site
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gamezone_site;

DROP TABLE IF EXISTS noticias;
DROP TABLE IF EXISTS lancamentos;
DROP TABLE IF EXISTS jogos;

CREATE TABLE noticias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(180) NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  data DATE NOT NULL,
  resumo TEXT NOT NULL,
  fonte VARCHAR(120) NOT NULL,
  link VARCHAR(255) NOT NULL
);

CREATE TABLE lancamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(180) NOT NULL,
  categoria VARCHAR(80) NOT NULL,
  data DATE NOT NULL,
  plataformas VARCHAR(180) NOT NULL,
  resumo TEXT NOT NULL,
  fonte VARCHAR(120) NOT NULL,
  link VARCHAR(255) NOT NULL
);

CREATE TABLE jogos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(180) NOT NULL,
  categoria VARCHAR(40) NOT NULL,
  nota DECIMAL(3,1) NOT NULL,
  resumo TEXT NOT NULL
);

INSERT INTO noticias (titulo, categoria, data, resumo, fonte, link) VALUES
('Forza Horizon 6 chegou ao Xbox, PC e Game Pass', 'Corrida', '2026-05-19', 'O novo jogo de corrida leva o festival Horizon para o Japão e ficou disponível para Xbox Series X|S, PC, Xbox Cloud e Game Pass.', 'Xbox Wire', 'https://news.xbox.com/en-us/2026/04/08/forza-horizon-6-preview-japan-collectibles-seamless-races-open-world-design/'),
('Subnautica 2 ganha roadmap de acesso antecipado', 'Survival', '2026-05-15', 'A equipe apresentou planos com melhorias de qualidade de vida, avanços no cooperativo, novos biomas e criaturas.', 'PC Gamer', 'https://www.pcgamer.com/games/survival-crafting/subnautica-2-roadmap-updates/'),
('Future Games Show terá Troy Baker e Alix Wilton Regan', 'Eventos', '2026-05-18', 'A apresentação de verão do Future Games Show está marcada para 6 de junho e terá dois atores conhecidos como apresentadores.', 'TechRadar', 'https://www.techradar.com/gaming/gaming-industry/its-all-shaping-up-to-be-a-wonderfully-daring-adventure-with-plenty-of-surprises-in-store-the-2026-future-games-show-summer-showcase-has-found-its-hosts-in-the-last-of-us-actor-troy-baker-and-tomb-raider-legacy-of-atlantis-alix-wilton-regan');

INSERT INTO lancamentos (titulo, categoria, data, plataformas, resumo, fonte, link) VALUES
('Yoshi and the Mysterious Book', 'Plataforma', '2026-05-21', 'Nintendo Switch 2', 'Novo jogo do Yoshi com visual de livro e foco em aventura de plataforma.', 'Gematsu', 'https://www.gematsu.com/2026/03/yoshi-and-the-mysterious-book-launches-may-21'),
('Coffee Talk Tokyo', 'Narrativo', '2026-05-21', 'PC, PlayStation, Xbox e Nintendo Switch', 'Simulador narrativo de cafeteria agora ambientado em Tóquio.', 'Gematsu', 'https://www.gematsu.com/2026/01/coffee-talk-tokyo-delayed-to-may-21'),
('007 First Light', 'Ação', '2026-05-27', 'PS5, Xbox Series X|S e PC', 'Novo jogo de James Bond desenvolvido pela IO Interactive.', 'Gematsu', 'https://www.gematsu.com/2025/12/007-first-light-delayed-to-may-27-2026');

INSERT INTO jogos (titulo, categoria, nota, resumo) VALUES
('Neon Strike', 'fps', 8.7, 'Combate rápido, mapas pequenos e partidas perfeitas para quem gosta de ação competitiva.'),
('Crystal Quest', 'rpg', 9.1, 'Guia inicial para montar personagens, escolher habilidades e evoluir sem perder recursos.'),
('Arena League', 'esports', 8.9, 'Times em destaque, próximos campeonatos e mudanças recentes no cenário competitivo.');

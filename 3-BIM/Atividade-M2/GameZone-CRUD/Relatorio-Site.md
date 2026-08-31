# Relatório – Desenvolvimento do GameZone-Site

## Instituição

**ETEC Vasco Antônio Venchiarutti**

## Curso

**Informática para Internet**

## Turma

**2°D**

## Autores

1. Bianca
2. Kevin
3. Gustavo
4. Davi

---

# Projeto – GameZone-Site

## Objetivo do projeto

O objetivo do projeto foi desenvolver um **portal gamer em PHP**, o GameZone, evoluindo de uma versão inicial em HTML estático para um site dinâmico com um **CRUD completo** de conteúdos (Notícias, Lançamentos e Jogos), armazenados em arquivos JSON.

O desenvolvimento foi realizado em grupo, com os integrantes participando de diferentes etapas do projeto, desde a conversão do site estático para PHP até a criação da área administrativa, validação dos formulários e ajustes finais.

---

## Funcionamento

O GameZone é composto por duas partes:

- **Site público:** Home, Jogos, Notícias, Lançamentos, Sobre e Contato, todas dinâmicas, exibindo o conteúdo cadastrado no CRUD.
- **Área administrativa:** painel em `admin/index.php` onde é possível cadastrar, listar, editar e excluir Notícias, Lançamentos e Jogos, com validação de campos obrigatórios.

Os dados são armazenados em arquivos JSON:

- `data/noticias.json`
- `data/lancamentos.json`
- `data/jogos.json`

Esses arquivos são lidos e gravados por funções centralizadas em `includes/data.php`.

---

## Desenvolvimento do projeto

O projeto foi desenvolvido em grupo, com cada integrante contribuindo em diferentes partes do sistema.

### Bianca

Bianca ficou responsável pela **validação de formulário e pelo polimento final** do projeto.

Adicionou a validação no servidor para impedir o cadastro de itens com campos obrigatórios em branco, ajustou o campo de seleção de categoria dos jogos para vir pré-preenchido corretamente ao editar e revisou o CSS da tabela administrativa para garantir a rolagem correta em telas de celular.

Também participou da revisão final do projeto, verificando o funcionamento das funcionalidades e contribuindo para a entrega da versão final.

### Kevin

Kevin ficou responsável pela **conversão das páginas públicas** do site.

Converteu `jogos.php`, `noticias.php`, `lancamentos.php`, `sobre.php` e `contato.php` de HTML estático para PHP, fazendo com que as páginas passassem a consumir os dados cadastrados no sistema.

Também realizou ajustes na ordenação da lista de lançamentos, no destaque visual da notícia mais recente e na marcação do item ativo no menu, contribuindo para que todas as páginas públicas funcionassem de maneira dinâmica.

### Gustavo

Gustavo foi responsável pela **conversão inicial da página inicial** de HTML estático para PHP dinâmico.

Criou a base do projeto em PHP, incluindo `includes/data.php` e `includes/layout.php`, e converteu a `index.php` para buscar as notícias mais recentes diretamente do arquivo JSON.

Também adicionou o painel de estatísticas, o card de destaque do próximo lançamento e a seção de jogos em destaque na página inicial, além de ajustes no menu de navegação para destacar corretamente a página atual.

### Davi

Davi ficou responsável pela **construção da área administrativa e implementação do CRUD**.

Criou o `admin/index.php`, com o painel de abas para Notícias, Lançamentos e Jogos, incluindo listagem em tabela, formulário de cadastro e edição.

Também implementou a geração de ID dos novos registros, a edição de itens existentes e a exclusão com confirmação.

Com isso, o sistema passou a permitir as principais operações do CRUD:

- Criar
- Listar
- Editar
- Excluir

Essas operações funcionam para os três tipos de conteúdo: Notícias, Lançamentos e Jogos.

---

## Tecnologias utilizadas

Durante o desenvolvimento foram utilizados:

- **PHP** — utilizado para o CRUD e para a lógica dinâmica das páginas;
- **HTML** — utilizado na estrutura inicial do site;
- **CSS** — utilizado para o design, responsividade e organização visual;
- **JSON** — utilizado para o armazenamento dos dados de Notícias, Lançamentos e Jogos;
- **JavaScript** — utilizado para interações no site, como menu e formulário de contato;
- **GitHub** — utilizado para armazenar o projeto e acompanhar seu desenvolvimento;
- **Inteligência Artificial (IA)** — utilizada como ferramenta de apoio durante o desenvolvimento.

---

## Uso de Inteligência Artificial

Durante o desenvolvimento do GameZone-Site, o grupo também utilizou ferramentas de **Inteligência Artificial (IA)** como recurso de apoio ao projeto.

A utilização da IA teve como objetivo auxiliar no desenvolvimento, na identificação de problemas e principalmente no aprendizado dos integrantes.

### Correção de erros

A IA foi utilizada para **identificar e corrigir erros** no código.

Quando surgiam problemas durante o desenvolvimento, o grupo utilizava a ferramenta para analisar possíveis causas dos erros, sugerir soluções e explicar o motivo pelo qual determinado código não estava funcionando corretamente.

Dessa forma, a IA serviu como um apoio durante o processo de depuração (*debug*).

### Estilização do projeto

Também utilizamos IA como auxílio para o **desenvolvimento e aprimoramento do estilo visual do site**.

Algumas partes do CSS foram desenvolvidas, ajustadas ou revisadas com auxílio da ferramenta, principalmente para melhorar:

- Organização dos elementos;
- Responsividade;
- Tabelas;
- Formulários;
- Cores;
- Espaçamentos;
- Aparência geral das páginas.

Nesse sentido, a IA também foi utilizada para **terceirizar parte do trabalho de estilização**, permitindo que o grupo concentrasse mais tempo na lógica do sistema, no PHP e na implementação do CRUD.

### Conhecimento e aprendizado

Além do desenvolvimento, a IA foi utilizada como **fonte de conhecimento e aprendizado**.

O grupo fez perguntas sobre PHP, HTML, CSS, JavaScript, JSON e conceitos relacionados ao CRUD para compreender melhor determinadas funcionalidades.

As respostas serviram como material de consulta para entender como implementar, modificar ou corrigir recursos do projeto.

É importante destacar que a IA foi utilizada como **ferramenta de auxílio e não como substituta completa do desenvolvimento do grupo**.

As sugestões fornecidas foram analisadas, adaptadas e aplicadas de acordo com as necessidades do GameZone-Site. O grupo foi responsável por integrar as soluções ao projeto, testar as funcionalidades e verificar se o resultado final estava funcionando corretamente.

Dessa maneira, o uso da Inteligência Artificial fez parte do processo de desenvolvimento como uma ferramenta complementar, contribuindo tanto para a resolução de problemas quanto para o aprendizado dos integrantes.

---

## Conclusão

Com o desenvolvimento do GameZone-Site, foi possível colocar em prática conhecimentos de **PHP, HTML, CSS e JavaScript**, além de organizar o trabalho em grupo e dividir as responsabilidades durante as diferentes etapas do projeto.

O projeto evoluiu de uma versão inicialmente estática para um site dinâmico com **CRUD completo**, passando pela conversão das páginas públicas, construção do painel administrativo, implementação das funcionalidades de cadastro, edição e exclusão, validação dos formulários e ajustes de usabilidade.

O trabalho em grupo permitiu que cada integrante contribuísse em diferentes partes do projeto, envolvendo desde a estrutura inicial até a entrega da versão final funcionando corretamente.

Além dos conhecimentos adquiridos nas tecnologias utilizadas, o projeto também proporcionou experiência com ferramentas de **Inteligência Artificial aplicadas ao desenvolvimento de software**.

A IA foi utilizada para auxiliar na correção de erros, no desenvolvimento e aprimoramento do estilo visual e na pesquisa de conhecimentos necessários para a implementação das funcionalidades.

Assim, o GameZone-Site não representou apenas a construção de um sistema funcional, mas também uma oportunidade de aprender a utilizar diferentes ferramentas de desenvolvimento, combinando os conhecimentos adquiridos durante o curso com ferramentas de apoio disponíveis atualmente.

---

**Relatório desenvolvido para fins educacionais no curso de Informática para Internet.**

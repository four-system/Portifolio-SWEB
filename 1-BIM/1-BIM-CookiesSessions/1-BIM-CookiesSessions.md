## Instituição 
`ETEC Vasco Antônio Venchiarutti`

---

## Curso
`Informática para Internet`

---

## Turma
`2°D`

---

## Autores
- `Bianca Rocha do Nascimento`
- `Davi Alves Bressan`
- `Gustavo Bastos da Silva`
- `Kevin Iamake Nozaki`

---

# Exercícios

## Exercício 1 – Diferença entre Cookies e Sessions

Cookies e sessions são formas de manter informações entre páginas em um site. Os *cookies* ficam armazenados no navegador do usuário, então eles continuam existindo mesmo depois que a pessoa fecha o site, dependendo do tempo definido. O problema é que, por estarem no computador do usuário, eles podem ser acessados e até alterados, o que diminui a segurança. Já as *sessions* funcionam diferente, porque os dados ficam guardados no servidor. O navegador recebe só um identificador, que serve para ligar o usuário aos dados salvos. Por isso, sessions são mais seguras e usadas em login e autenticação. No geral, cookies são mais usados para coisas simples, como preferências, enquanto sessions são melhores para dados importantes.

---

## Exercício 2 – Aplicação em loja virtual

Em uma loja virtual, dá pra usar cookies e sessions juntos tranquilamente. As *sessions* são boas para manter o usuário logado, porque guardam os dados no servidor e são mais seguras. Também fazem sentido no carrinho de compras, já que os produtos adicionados são temporários e não devem ser facilmente alterados pelo usuário. Já os *cookies* podem ser usados para salvar preferências, como idioma do site, tema escuro ou até produtos que a pessoa viu recentemente. Isso ajuda a melhorar a experiência do usuário sem precisar de muita 
segurança. Assim, cada um tem sua função: sessions cuidam da parte mais importante e cookies ajudam na personalização.

---

## Exercício 3 – Investigação sobre cookies

Quando o arquivo foi executado pela primeira vez, apareceu a mensagem "Cookie ainda não disponível". Isso acontece porque o cookie é criado pelo servidor, mas o navegador ainda não teve tempo de enviar ele de volta. Só depois de atualizar a página é que o cookie passa a existir de verdade na requisição. Após dar o F5, o valor começou a aparecer normalmente na tela. Ao abrir as ferramentas do navegador, foi possível ver o cookie salvo com o nome "contador". Depois que os cookies foram apagados e a página foi atualizada de novo, voltou a aparecer a mensagem inicial. Isso mostra que os cookies dependem totalmente do navegador para existir e funcionar corretamente.

## Exercício 4 – Reflexão sobre uso de sessions


As sessions são mais usadas em login porque são mais seguras que os cookies. Isso acontece porque os dados não ficam no navegador, e sim no servidor, o que dificulta alguém mexer neles. O navegador só recebe um código de identificação da sessão, então não tem acesso direto às informações importantes. Além disso, o servidor pode controlar melhor quando a sessão começa e termina, como no caso de logout. Se fosse só com cookies, o usuário poderia alterar os dados manualmente, o que abriria várias falhas de segurança. Isso poderia permitir acessos indevidos ou até invasões. Por isso, sessions são a melhor escolha para proteger dados sensíveis.

---

## Referências

- https://www.w3schools.com/php/php_cookies.asp
- https://www.w3schools.com/php/php_sessions.asp
- https://www.php.net/manual/pt_BR/function.setcookie.php  
- https://www.php.net/manual/pt_BR/reserved.variables.cookies.php  
- https://www.php.net/manual/pt_BR/function.session-start.php  
- https://www.php.net/manual/pt_BR/reserved.variables.session.php

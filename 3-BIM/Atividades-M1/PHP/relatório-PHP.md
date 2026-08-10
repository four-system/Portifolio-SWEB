# Relatório – Criptografia no PHP

## Instituição

*ETEC Vasco Antônio Venchiarutti*

## Curso

*Informática para Internet*

## Turma

*2°D*

## Autores

- Gustavo
- Kevin
- Davi
- Bianca

---

# Projeto – Criptografia no PHP

## Objetivo do projeto

O objetivo do projeto foi desenvolver uma *página em PHP sobre criptografia*, apresentando alguns dos principais métodos e recursos relacionados à proteção e transformação de informações disponíveis no PHP.

A página foi criada para demonstrar de forma prática como diferentes métodos de criptografia, hash e codificação funcionam, além de apresentar suas principais características e diferenças.

Durante o projeto, foram pesquisados métodos como:

- MD5;
- SHA-1;
- SHA-256;
- password_hash;
- Base64.

Além da parte de pesquisa, também foi desenvolvida uma área prática para que o usuário pudesse testar diferentes formas de transformação de textos.

---

## Funcionamento

A página apresenta informações sobre diferentes métodos utilizados para criptografia, hash e codificação de dados.

O usuário pode conhecer como cada método funciona e também utilizar a área de testes para inserir um texto e verificar o resultado utilizando diferentes métodos.

Entre os recursos apresentados estão:

- MD5;
- SHA-1;
- SHA-256;
- password_hash;
- Base64;
- Comparação entre os métodos;
- Área para testar os métodos de forma prática.

A página também apresenta informações sobre as principais características de cada método, permitindo entender quais possuem maior segurança e quais não são recomendados atualmente para proteger senhas.

---

## Desenvolvimento do projeto

O projeto foi desenvolvido em grupo, com cada integrante responsável por diferentes partes.

### Gustavo

Gustavo foi responsável pela *base do projeto junto com Kevin* e também realizou ajustes e correções no código durante o desenvolvimento.

Além disso, Gustavo desenvolveu a parte em *PHP responsável por realizar os processos de criptografia, hash e codificação*, fazendo com que os métodos escolhidos funcionassem de forma prática na página.

### Kevin

Kevin ajudou Gustavo na *criação da base do projeto*, contribuindo para a estrutura inicial e para a organização do código.

### Davi

Davi ficou responsável pelo *CSS do projeto*, trabalhando na aparência da página, organização dos elementos e estilização da interface.

### Bianca

Bianca ficou responsável pela *pesquisa sobre os métodos apresentados no projeto*.

Foram pesquisados:

- MD5;
- SHA-1;
- SHA-256;
- password_hash;
- Base64;
- Comparativos entre os métodos.

As informações pesquisadas foram utilizadas para explicar as características, funcionamento e diferenças entre cada método apresentado no site.

---

## Métodos apresentados

### MD5

O *MD5* é uma função de hash que transforma uma informação em uma sequência de caracteres com tamanho fixo.

Apesar de ter sido muito utilizado no passado, atualmente o MD5 não é considerado seguro para proteger senhas ou informações que precisam de segurança, pois existem formas conhecidas de encontrar colisões.

### SHA-1

O *SHA-1* também é uma função de hash utilizada para transformar informações em uma sequência de caracteres.

Assim como o MD5, o SHA-1 possui problemas de segurança conhecidos e não é recomendado para proteger senhas atualmente.

### SHA-256

O *SHA-256* faz parte da família SHA-2 e gera um hash de 256 bits.

Ele é considerado muito mais seguro que MD5 e SHA-1 para diversas aplicações, porém não deve ser utilizado diretamente para armazenar senhas. Para senhas, existem funções específicas como o password_hash.

### password_hash

O password_hash é um recurso do PHP desenvolvido especificamente para realizar o hash de senhas de maneira mais segura.

Ele utiliza algoritmos apropriados para armazenamento de senhas e também trabalha com mecanismos como o salt, dificultando ataques contra as senhas armazenadas.

### Base64

O *Base64* é diferente dos métodos de hash apresentados.

Ele é uma forma de *codificação*, utilizada para representar dados utilizando caracteres específicos.

Por não ser um método de criptografia, qualquer pessoa que tenha o texto codificado pode realizar a decodificação. Portanto, Base64 não deve ser utilizado como forma de proteger senhas ou informações secretas.

---

## Comparação dos métodos

| Método | Tipo | Pode ser revertido? | Uso para senhas |
|---|---|---|---|
| MD5 | Hash | Não diretamente | Não recomendado |
| SHA-1 | Hash | Não diretamente | Não recomendado |
| SHA-256 | Hash | Não diretamente | Não recomendado sozinho |
| password_hash | Hash de senha | Não | Recomendado |
| Base64 | Codificação | Sim | Não recomendado |

---

## Tecnologias utilizadas

Durante o desenvolvimento foram utilizados:

- *PHP* para a programação e funcionamento dos métodos;
- *HTML* para a estrutura da página;
- *CSS* para o design e organização visual;
- *GitHub* para armazenar e acompanhar o projeto.

---

## Prints da página

### Página inicial

![Página inicial](./imagens/pagina-inicial.png)

### Página de informações

![Informações sobre criptografia](./imagens/informacoes.png)

### Área de testes

![Área de testes](./imagens/teste-criptografia.png)

---

## Prints do código

### Código PHP

![Código PHP](./imagens/codigo-php.png)

### Código CSS

![Código CSS](./imagens/codigo-css.png)

### Estrutura do projeto

![Estrutura do projeto](./imagens/estrutura.png)

---

## Conclusão

Com o desenvolvimento do projeto, foi possível aprender mais sobre *criptografia, hash e codificação*, além de colocar em prática conhecimentos de PHP, HTML e CSS.

A pesquisa permitiu entender que diferentes métodos possuem finalidades diferentes. Também foi possível perceber que métodos antigos, como MD5 e SHA-1, não são recomendados para proteger senhas, enquanto recursos específicos como password_hash são mais adequados para essa finalidade.

A criação da área de testes também ajudou a entender de forma prática como os textos são transformados pelos diferentes métodos.

O trabalho em grupo permitiu que cada integrante contribuísse em uma parte do projeto, envolvendo programação, design, pesquisa e organização da página.

---

Relatório desenvolvido para fins educacionais no curso de Informática para Internet.

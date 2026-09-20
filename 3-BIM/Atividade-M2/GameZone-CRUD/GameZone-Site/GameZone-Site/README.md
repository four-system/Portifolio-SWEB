# GameZone-Site

Projeto escolar de site gamer com página pública para o cliente e área administrativa com CRUD completo.

## Páginas públicas

- `index.php`: página inicial visível para o cliente.
- `noticias.php`: lista notícias cadastradas.
- `lancamentos.php`: mostra próximos lançamentos cadastrados.
- `jogos.php`: mostra jogos/reviews cadastrados.
- `sobre.php`: apresenta proposta, público-alvo, mobile first e identidade visual.
- `contato.php`: formulário visual de contato.

## Área administrativa

Acesse:

```text
admin/index.php
```

CRUDs disponíveis:

- Notícias: criar, listar, editar e excluir.
- Lançamentos: criar, listar, editar e excluir.
- Jogos: criar, listar, editar e excluir.

Os dados são salvos em banco de dados MySQL/MariaDB usando PDO.

## Como rodar

1. Coloque a pasta em um servidor com PHP, como XAMPP, WampServer ou Laragon.
2. Abra o phpMyAdmin.
3. Importe o arquivo `database.sql`.
4. Confira os dados de conexão em `config/database.php`.
5. Abra:

```text
http://localhost/GameZone-Site/index.php
```

Para administrar:

```text
http://localhost/GameZone-Site/admin/index.php
```

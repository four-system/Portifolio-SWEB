SISTEMA DE GESTÃO PARA GRÁFICA — VERSÃO COM PERFIS

Módulos:
- Administrador: cadastro/edição/desativação de usuários, consulta ao estoque e vendas.
- Gerente: cadastro e edição de produtos, movimentações de estoque, acompanhamento de vendas e atualização do status dos pedidos.
- Funcionário: registra vendas/pedidos e consulta as próprias vendas.
- Ao registrar venda, o sistema verifica estoque, grava os itens e reduz quantidades em uma transação.

INSTALAÇÃO LOCAL (XAMPP)
1. Extraia a pasta grafica em C:\xampp\htdocs\ (ou renomeie como desejar).
2. Inicie Apache e MySQL no XAMPP.
3. Importe banco.sql no phpMyAdmin.
4. Confira config/conexao.php para credenciais do MySQL.
5. Acesse http://localhost/grafica/

CRIAR O PRIMEIRO ADMINISTRADOR
Execute no terminal PHP do XAMPP (ajuste os dados):
php -r "echo password_hash('TroqueEstaSenha123', PASSWORD_DEFAULT);"
Copie o hash e execute no SQL do phpMyAdmin:
INSERT INTO usuarios (nome,login,senha,nivel,ativo) VALUES ('Administrador','admin','COLE_HASH_AQUI','administrador',1);
Faça login com admin e a senha escolhida no hash.

OBSERVAÇÕES
- Não há botão de exclusão definitiva de usuários: o sistema desativa acessos, preservando o histórico.
- A senha nunca é exibida. Na edição, deixe o campo de senha vazio para mantê-la.
- Faça backup antes de instalar em banco que já contém dados. O banco.sql cria as novas tabelas, mas não migra uma tabela usuarios existente além da estrutura original compatível.
- Antes de colocar online, configure HTTPS, senha forte do banco, backups e validações adicionais de segurança.

NOVOS MÓDULOS — CLIENTES E PEDIDOS GRÁFICOS
- Clientes: cadastro, consulta e edição de nome/razão social, telefone e e-mail.
- Pedidos: cliente, serviço, descrição do material, quantidade, data, prazo, valor e funcionário responsável.
- Acompanhamento: busca por número, nome do cliente e status; atualização entre recebido, em produção, pronto, entregue e cancelado.
- Os módulos ficam disponíveis no painel para os três perfis. O funcionário responsável pode ser qualquer usuário ativo.
- Para instalação nova, importe o banco.sql atualizado. Se o banco já existe, faça backup e execute somente os dois CREATE TABLE novos (clientes e pedidos) ao final de banco.sql no banco grafica_db.

CORREÇÃO DO ERRO 23000 NO CADASTRO DE USUÁRIOS
- O campo usuarios.login possui índice UNIQUE. Se for informado um login já existente, o MySQL retorna SQLSTATE 23000 / erro 1062.
- usuarios.php agora identifica especificamente o erro 1062 e informa que o login já está em uso.
- Outras violações de integridade ficam registradas no log de erros do PHP com SQLSTATE e código MySQL para diagnóstico.

CORREÇÃO DO ERRO "Este login/e-mail já está cadastrado" (CAUSA REAL)
- O banco.sql original não tinha a coluna usuarios.email, mas o banco em uso tem (índice UNIQUE `email`). O INSERT do sistema não informava e-mail, então a coluna recebia o valor padrão '' (vazio). O primeiro usuário ocupava o '' e todos os cadastros seguintes colidiam no UNIQUE do e-mail (erro 1062), mesmo com login e e-mail novos.
- usuarios.php agora detecta a coluna email, grava NULL (ou o login, se a coluna ainda for NOT NULL), valida login e e-mail separadamente, ignora o próprio registro na edição e distingue 1062 (login/e-mail) de outros erros. Detalhes técnicos vão só para o log do PHP.
- Para corrigir definitivamente o banco existente, execute migracao_usuarios_email.sql (faça backup antes).

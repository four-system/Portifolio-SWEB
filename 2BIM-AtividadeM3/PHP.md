# Funções para Criptografia, Hash, Codificação e Proteção de Dados em PHP
**Integrantes:** Davi Bressan, Gustavo Bastos, Bianca Rocha, Kevin Nozaki.
**Curso:** 2°D

## Resumo

Este trabalho apresenta uma pesquisa sobre recursos da linguagem PHP utilizados para proteger informações em aplicações web. São abordados conceitos de segurança da informação, diferenças entre criptografia, hash e codificação, funções nativas do PHP para senhas, Base64, OpenSSL, prevenção de ataques e boas práticas de desenvolvimento seguro.

## 1. Segurança em aplicações web

Segurança da informação é o conjunto de práticas, controles e tecnologias usadas para preservar a confidencialidade, a integridade e a disponibilidade dos dados. Em uma aplicação web, isso significa impedir que informações de usuários sejam acessadas por pessoas não autorizadas, alteradas indevidamente ou deixem de estar disponíveis quando necessário.

Proteger dados dos usuários é importante por motivos técnicos, legais e éticos. Em sistemas reais, dados como nome, e-mail, senha, CPF, endereço, histórico de compras e informações financeiras podem causar danos quando vazados. A Autoridade Nacional de Proteção de Dados recomenda que organizações adotem medidas de segurança para proteger dados pessoais e reduzir riscos no tratamento dessas informações (ANPD, 2025). O CERT.br também destaca a importância de cuidados com contas, senhas, privacidade e proteção contra golpes na Internet (CERT.br, 2026).

Os principais riscos em aplicações desenvolvidas para a Internet incluem:

- roubo de senhas;
- vazamento de dados pessoais;
- SQL Injection;
- Cross-Site Scripting (XSS);
- Cross-Site Request Forgery (CSRF);
- sequestro de sessão;
- uso de bibliotecas ou versões desatualizadas;
- exposição de arquivos de configuração;
- falhas de validação de entrada;
- ausência de HTTPS.

No PHP, a segurança depende tanto dos recursos da linguagem quanto das decisões do desenvolvedor. A própria documentação oficial possui uma seção dedicada a segurança, incluindo segurança de banco de dados, dados enviados pelo usuário, sessões e manutenção da versão atualizada (PHP, 2026a).

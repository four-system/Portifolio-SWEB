-- MIGRAÇÃO SEGURA: coluna usuarios.email (não apaga tabelas nem registros). Faça backup antes.
USE grafica_db;

-- 1) DIAGNÓSTICO (somente leitura): confirme a causa no seu banco.
SHOW COLUMNS FROM usuarios LIKE 'email';          -- veja Null e Default
SELECT id, nome, login, email FROM usuarios;       -- veja se há email vazio ('')

-- 2) Se a coluna email existir (seu caso): permitir NULL e converter '' em NULL.
--    O UNIQUE continua valendo para e-mails reais; vários usuários sem e-mail passam a ser permitidos.
ALTER TABLE usuarios MODIFY email VARCHAR(150) NULL DEFAULT NULL;
UPDATE usuarios SET email = NULL WHERE TRIM(email) = '';

-- 3) Se a coluna NÃO existir (apenas se o passo 1 não retornou linha):
-- ALTER TABLE usuarios ADD COLUMN email VARCHAR(150) NULL DEFAULT NULL UNIQUE AFTER login;

<?php

$texto = $_POST['texto'] ?? '';

if ($texto === '') {
    header("Location: index.html");
    exit;
}

$md5 = md5($texto);
$sha1 = sha1($texto);
$sha256 = hash('sha256', $texto);
$password = password_hash($texto, PASSWORD_DEFAULT);
$base64 = base64_encode($texto);

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

```
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Teste de Criptografia</title>

<link rel="stylesheet" href="style.css">
```

</head>

<body>

```
<header>

    <h1>Teste de Criptografia</h1>

    <p>
        Veja como o mesmo texto é transformado por diferentes métodos.
    </p>

</header>


<main>

    <section class="texto-original">

        <h2>Texto original</h2>

        <div class="texto">
            <?= htmlspecialchars($texto) ?>
        </div>

    </section>


    <section>

        <h2>Resultados</h2>

        <div class="resultado">

            <div class="resultado-card">

                <div class="titulo">
                    <h3>MD5</h3>
                    <span>Hash • 128 bits</span>
                </div>

                <p>
                    <?= htmlspecialchars($md5) ?>
                </p>

            </div>


            <div class="resultado-card">

                <div class="titulo">
                    <h3>SHA-1</h3>
                    <span>Hash • 160 bits</span>
                </div>

                <p>
                    <?= htmlspecialchars($sha1) ?>
                </p>

            </div>


            <div class="resultado-card destaque">

                <div class="titulo">
                    <h3>SHA-256</h3>
                    <span>Hash • 256 bits</span>
                </div>

                <p>
                    <?= htmlspecialchars($sha256) ?>
                </p>

            </div>


            <div class="resultado-card">

                <div class="titulo">
                    <h3>password_hash()</h3>
                    <span>Hash para senhas</span>
                </div>

                <p>
                    <?= htmlspecialchars($password) ?>
                </p>

            </div>


            <div class="resultado-card">

                <div class="titulo">
                    <h3>Base64</h3>
                    <span>Codificação</span>
                </div>

                <p>
                    <?= htmlspecialchars($base64) ?>
                </p>

            </div>

        </div>

    </section>


    <section class="explicacao">

        <h2>O que podemos observar?</h2>

        <p>
            O mesmo texto gera resultados completamente diferentes
            dependendo do método utilizado. Os métodos de hash não
            foram feitos para simplesmente recuperar o texto original.
        </p>

        <p>
            Já o Base64 funciona de outra maneira: ele apenas codifica
            os dados, permitindo que sejam decodificados novamente.
            Por isso, Base64 não deve ser considerado um método de
            proteção de informações.
        </p>

    </section>


    <div class="acoes">

        <a href="index.html" class="botao">
            ← Voltar para a pesquisa
        </a>

       

    </div>

</main>


<footer>

    <p>
        Demonstração de criptografia e hash utilizando PHP
    </p>

</footer>
```

</body>

</html>

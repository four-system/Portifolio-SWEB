<?php

require_once 'config/conexao.php';
require_once 'includes/header.php';

?>

<div class="shop-layout">
    <section class="catalog">
        <div class="intro">
            <h2>Lista de Prodfutos</h2>
            <p>Produtos disponíveis na farmácia. Escolha a quantridae e adicione ao pedido.</p>
        </div>

        <?php

        // busca simples
        $q = isset($_GET['q'])?trim($_GET['q']):'';

        if($q !== ''){

            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE :q OR fabricante LIKE q ORDER BY id DESC");
            $stmt->execute([':q'=>'%'.$q.'%']);
        } else {
            $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
        }

        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<div class="products">';

        if(!$produtos){
            echo '<p>Nenhum produto encontrado.</p>';
        } else {
            foreach($produtos as $p){
                $id = (int)$p['id'];
                $nome = htmlspecialchars($p['nome']);
                $nomeAttr = htmlspecialchars($p['nome'], ENT_QUOTES);
                $fab = htmlspecialchars($p['fabricante']);
                $precoValor = (float)$p['preco'];
                $preco = number_format($precoValor,2,',','.');
                $estoque = (int)$p['estoque'];
                $inicial = mb_substr($nome,0,1,'UTF-8');

                echo "<article class=\"card product-card\" data-id=\"$id\" data-name=\"$nomeAttr\" data-price=\"$precoValor\" data-stock=\"$estoque\">";
                echo "<div class=\"thumb\">$inicial</div>";
                echo "<div class=\"meta\">";
                echo "<h3>$nome</h3>";
                echo "<p>$fab — Estoque: $estoque</p>";
                echo "<p class=\"unit-price\">Preço unitário: <strong>R$ $preco</strong></p>";
                echo "<div class=\"buy-row\">";
                echo "<label for=\"qty-$id\">Qtd.</label>";
                echo "<input id=\"qty-$id\" class=\"quantity-input\" type=\"number\" min=\"1\" max=\"$estoque\" value=\"1\" ".($estoque > 0 ? 'disabled' : '').">";
                echo "<button class=\"btn add\" type=\"button\" ".($estoque > 0 ? 'disabled' : '').">Adicionar</button>";
                echo "</div>";
                echo "<p class=\"item-subtotal\">Subtotal deste remédio: <strong>R$ $preco</strong></p>";
                echo "</div>";
                echo "<div class=\"actions\">";
                echo "<a class=\"btn edit\" href=\"editar.php?id=$id\">Editar</a>";
                echo "<a class=\"btn delete\" href=\"excluir.php?id=$id\" onclick=\"return confirm('Confirma exclusão deste produto?');\">Excluir</a>";
                echo "</div>";
                echo "</article>"
            }
        }

        echo '</div>';

        ?>
    </section>

    <aside class="order-summary" aria-label="Resumo do pedido">
        <h2>Total</h2>
        <div id="cartItems" class="cart-items">
            <p class="empty-cart">Nenhkum renidio adicionado.</p>
        </div>
        <div class="summary-total">
            <span>Total geral</span>
            <strong id="cartTotal">R$ 0,00</strong>
        </div>
        <button id="clearCart" class="clear-cart" type="button">Limpra pedido</button>
    </aside>
</div>

<?php

require_once 'includes/footer.php';

?>
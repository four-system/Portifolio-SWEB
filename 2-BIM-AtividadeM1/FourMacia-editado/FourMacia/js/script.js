document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');
    var cart = {};
    var currency = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    if(toggle && nav){
        toggle.addEventListener('click', function(){
            nav.classList.toggle('open');
        });
    }

    function formatMoney(value){
        return currency.format(value);
    }

    function updateCardSubtotal(card){
        var price = Number(card.dataset.price);
        var input = card.querySelector('.quantity-input');
        var subtotal = card.querySelector('.item-subtotal strong');
        var quantity = Math.max(1, Number(input.value || 1));

        input.value = quantity;
        subtotal.textContent = formatMoney(price * quantity);
    }

    function renderCart(){
        var cartItems = document.getElementById('cartItems');
        var cartTotal = document.getElementById('cartTotal');
        var entries = Object.values(cart);
        var total = entries.reduce(function(sum, item){
            return sum + (item.price * item.quantity);
        }, 0);

        if(!cartItems || !cartTotal){
            return;
        }

        if(entries.length === 0){
            cartItems.innerHTML = '<p class="empty-cart">Nenhum remédio adicionado.</p>';
        } else {
            cartItems.innerHTML = entries.map(function(item){
                var subtotal = item.price * item.quantity;

                return '<div class="cart-item">' +
                    '<div>' +
                        '<strong>' + item.name + '</strong>' +
                        '<span>' + item.quantity + 'x ' + formatMoney(item.price) + '</span>' +
                    '</div>' +
                    '<strong>' + formatMoney(subtotal) + '</strong>' +
                '</div>';
            }).join('');
        }

        cartTotal.textContent = formatMoney(total);
    }

    document.querySelectorAll('.product-card').forEach(function(card){
        var input = card.querySelector('.quantity-input');
        var addButton = card.querySelector('.btn.add');

        if(input){
            input.addEventListener('input', function(){
                updateCardSubtotal(card);
            });
        }

        if(addButton){
            addButton.addEventListener('click', function(){
                var id = card.dataset.id;
                var quantity = Math.max(1, Number(input.value || 1));
                var max = Number(card.dataset.stock || input.max || quantity);

                if(max > 0 && quantity > max){
                    quantity = max;
                    input.value = max;
                }

                var currentQuantity = cart[id] ? cart[id].quantity : 0;
                var finalQuantity = currentQuantity + quantity;

                if(max > 0 && finalQuantity > max){
                    finalQuantity = max;
                }

                cart[id] = {
                    name: card.dataset.name,
                    price: Number(card.dataset.price),
                    quantity: finalQuantity
                };

                renderCart();
            });
        }

        updateCardSubtotal(card);
    });

    var clearCart = document.getElementById('clearCart');
    if(clearCart){
        clearCart.addEventListener('click', function(){
            cart = {};
            renderCart();
        });
    }
});

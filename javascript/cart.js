(function() {
    function updateCartCount() {
        fetch('/api/cart/get')
            .then(res => res.json())
            .then(data => {
                const cartCountEl = document.getElementById('cartCount');
                if (cartCountEl && data.count) {
                    cartCountEl.textContent = data.count;
                }
            })
            .catch(err => console.error('Cart error:', err));
    }
    
    function addToCart(pressingId, quantity = 1) {
        fetch('/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pressing_id: pressingId, quantity: quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                alert('🎵 Товар добавлен в корзину!');
            } else {
                alert('Ошибка: ' + (data.error || 'попробуйте позже'));
            }
        })
        .catch(err => console.error('Add to cart error:', err));
    }
    
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-preorder') || 
            e.target.classList.contains('add-to-cart')) {
            const pressingId = e.target.dataset.pressingId;
            if (pressingId) {
                addToCart(parseInt(pressingId));
            }
        }
    });
    
    updateCartCount();
})();

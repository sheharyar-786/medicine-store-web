// Simple script to handle 'Add to Cart' via AJAX
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.id;

        fetch('actions/add_to_cart_action.php', {
            method: 'POST',
            body: new URLSearchParams({'product_id': productId})
        })
        .then(res => res.text())
        .then(data => {
            alert("Added to cart!");
            // You could update a cart counter in the header here
        });
    });
});
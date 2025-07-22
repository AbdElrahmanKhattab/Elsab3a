var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

// Cart logic using localStorage
const CART_KEY = 'elsab3a_cart';

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
}
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}
function addToCart(product) {
    let cart = getCart();
    const idx = cart.findIndex(item => item.id === product.id);
    if (idx > -1) {
        cart[idx].quantity += 1;
    } else {
        cart.push({...product, quantity: 1});
    }
    saveCart(cart);
    updateCartCount();
}
function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
    updateCartCount();
}
function updateCartQuantity(productId, qty) {
    let cart = getCart();
    const idx = cart.findIndex(item => item.id === productId);
    if (idx > -1) {
        cart[idx].quantity = qty;
        if (cart[idx].quantity < 1) cart.splice(idx, 1);
        saveCart(cart);
        updateCartCount();
    }
}
function clearCart() {
    localStorage.removeItem(CART_KEY);
    updateCartCount();
}
function updateCartCount() {
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) cartCount.textContent = count > 0 ? count : '';
}
// Event hooks for UI (to be used in store.html)
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    // Plus buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            const product = JSON.parse(btn.dataset.product);
            addToCart(product);
        });
    });
    // Cart icon
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', () => {
            showCartModal();
        });
    }
});
// Placeholder for showCartModal (to be implemented in store.html)
function showCartModal() {}
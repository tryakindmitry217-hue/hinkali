// cart.js - Функции корзины
const cartFunctions = {
    getCart() {
        return JSON.parse(localStorage.getItem('foodDeliveryCart')) || [];
    },
    
    saveCart(cart) {
        localStorage.setItem('foodDeliveryCart', JSON.stringify(cart));
        this.dispatchCartUpdate();
    },
    
    addToCart(item) {
        const cart = this.getCart();
        const existingItem = cart.find(i => i.name === item.name);
        
        if (existingItem) {
            existingItem.quantity += item.quantity;
        } else {
            cart.push({
                ...item,
                id: Date.now() + Math.random().toString(36).substr(2, 9)
            });
        }
        
        this.saveCart(cart);
        return cart;
    },
    
    removeFromCart(itemId) {
        const cart = this.getCart();
        const updatedCart = cart.filter(item => item.id !== itemId);
        this.saveCart(updatedCart);
        return updatedCart;
    },
    
    updateQuantity(itemId, quantity) {
        const cart = this.getCart();
        const item = cart.find(i => i.id === itemId);
        
        if (item) {
            item.quantity = quantity;
            this.saveCart(cart);
        }
        
        return cart;
    },
    
    clearCart() {
        localStorage.removeItem('foodDeliveryCart');
        this.dispatchCartUpdate();
        return [];
    },
    
    getTotalPrice() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    
    getTotalItems() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + item.quantity, 0);
    },
    
    dispatchCartUpdate() {
        // Создаем кастомное событие для обновления UI
        const event = new CustomEvent('cartUpdated', {
            detail: { cart: this.getCart() }
        });
        document.dispatchEvent(event);
    }
};

// Экспорт для использования в других файлах
if (typeof module !== 'undefined' && module.exports) {
    module.exports = cartFunctions;
}

// Инициализация корзины при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // Создаем глобальную ссылку на функции корзины
    window.cartFunctions = cartFunctions;
    
    // Обработчик обновления корзины
    document.addEventListener('cartUpdated', function(e) {
        // Можно добавить дополнительные действия при обновлении корзины
        console.log('Корзина обновлена:', e.detail.cart);
    });
    
    // Инициализация счетчика корзины в шапке
    function updateCartCounter() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const totalItems = cartFunctions.getTotalItems();
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.style.display = 'flex';
            } else {
                cartCount.style.display = 'none';
            }
        }
    }
    
    updateCartCounter();
});
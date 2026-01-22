// forms.js - добавляем функции для работы с сервером
(function() {
    'use strict';
    
    // ====== ФУНКЦИИ ДЛЯ РАБОТЫ С API ======
    
    const API = {
        // Отправка запроса
        async request(endpoint, data) {
            try {
                const formData = new FormData();
                for (const key in data) {
                    formData.append(key, data[key]);
                }
                
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                return { success: false, message: 'Ошибка соединения с сервером' };
            }
        },
        
        // Регистрация
        async register(userData) {
            return await this.request('auth_handler.php', {
                action: 'register',
                ...userData
            });
        },
        
        // Вход
        async login(email, password, remember = false) {
            return await this.request('auth_handler.php', {
                action: 'login',
                email: email,
                password: password,
                remember: remember ? '1' : '0'
            });
        },
        
        // Выход
        async logout() {
            return await this.request('auth_handler.php', {
                action: 'logout'
            });
        },
        
        // Сохранение заказа
        async saveOrder(orderData) {
            return await this.request('order_handler.php', orderData);
        }
    };
    
    // ====== ОБНОВЛЕННАЯ ФУНКЦИЯ РЕГИСТРАЦИИ ======
    function updateRegisterForm() {
        const registerForm = document.getElementById('register-form');
        if (!registerForm) return;
        
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.register-btn');
            const originalText = submitBtn.textContent;
            
            // Сбор данных
            const userData = {
                name: document.getElementById('register-name').value.trim(),
                email: document.getElementById('register-email').value.trim(),
                phone: document.getElementById('register-phone').value.trim(),
                password: document.getElementById('register-password').value,
                confirm_password: document.getElementById('register-confirm').value
            };
            
            // Валидация
            const errors = [];
            if (!userData.name) errors.push('Введите имя');
            if (!userData.email) errors.push('Введите email');
            if (!userData.phone) errors.push('Введите телефон');
            if (!userData.password) errors.push('Введите пароль');
            if (userData.password !== userData.confirm_password) {
                errors.push('Пароли не совпадают');
            }
            
            if (errors.length > 0) {
                showNotification(errors.join('<br>'), 'error');
                return;
            }
            
            // Отправка на сервер
            submitBtn.textContent = 'Регистрация...';
            submitBtn.disabled = true;
            
            const result = await API.register(userData);
            
            if (result.success) {
                showNotification(result.message, 'success');
                
                // Сохраняем данные пользователя
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('isLoggedIn', 'true');
                
                // Закрываем модальное окно
                closeAuthModal();
                
                // Обновляем интерфейс
                updateUserInterface(result.user);
                
                // Очищаем форму
                registerForm.reset();
            } else {
                showNotification(result.message, 'error');
            }
            
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // ====== ОБНОВЛЕННАЯ ФУНКЦИЯ ВХОДА ======
    function updateLoginForm() {
        const loginForm = document.getElementById('login-form');
        if (!loginForm) return;
        
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.login-btn');
            const originalText = submitBtn.textContent;
            
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            const remember = document.getElementById('remember-me')?.checked || false;
            
            if (!email || !password) {
                showNotification('Заполните все поля', 'error');
                return;
            }
            
            submitBtn.textContent = 'Вход...';
            submitBtn.disabled = true;
            
            const result = await API.login(email, password, remember);
            
            if (result.success) {
                showNotification(result.message, 'success');
                
                // Сохраняем данные пользователя
                localStorage.setItem('user', JSON.stringify(result.user));
                localStorage.setItem('isLoggedIn', 'true');
                
                // Закрываем модальное окно
                closeAuthModal();
                
                // Обновляем интерфейс
                updateUserInterface(result.user);
                
                // Очищаем форму
                loginForm.reset();
            } else {
                showNotification(result.message, 'error');
            }
            
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // ====== ОБНОВЛЕННАЯ ФУНКЦИЯ ЗАКАЗА ======
    function updateOrderForm() {
        const orderForm = document.getElementById('checkout-form');
        if (!orderForm) return;
        
        orderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Проверка корзины
            const cart = window.cartFunctions ? window.cartFunctions.getCart() : [];
            if (cart.length === 0) {
                showNotification('Корзина пуста', 'error');
                return;
            }
            
            // Сбор данных
            const orderData = {
                name: document.getElementById('order-name').value,
                phone: document.getElementById('order-phone').value,
                address: document.getElementById('order-address').value,
                time: document.getElementById('order-time').value,
                comment: document.getElementById('order-comment').value,
                payment: document.querySelector('input[name="payment"]:checked')?.value || 'cash',
                cart: JSON.stringify(cart)
            };
            
            // Валидация
            const required = ['name', 'phone', 'address', 'time'];
            const errors = [];
            
            required.forEach(field => {
                if (!orderData[field]) {
                    errors.push(`Поле "${field}" обязательно`);
                }
            });
            
            if (errors.length > 0) {
                showNotification(errors.join('<br>'), 'error');
                return;
            }
            
            // Отправка заказа
            const submitBtn = document.getElementById('submit-order-btn');
            const originalText = submitBtn.value;
            
            submitBtn.value = 'Отправка...';
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            
            const result = await API.saveOrder(orderData);
            
            if (result.success) {
                // Показываем успешное сообщение
                const successNotification = document.querySelector('.sb-notification_success');
                if (successNotification) {
                    successNotification.innerHTML = `
                        <div class="success-animation">
                            <div class="success-icon">✓</div>
                            <h4>Заказ №${result.order.number} успешно оформлен!</h4>
                            <p>Мы свяжемся с вами в ближайшее время.</p>
                            <p class="order-summary">
                                Номер заказа: <strong>${result.order.number}</strong><br>
                                Сумма заказа: <strong>${result.order.total}₽</strong><br>
                                Статус: <strong>Обрабатывается</strong>
                            </p>
                        </div>
                    `;
                    successNotification.style.display = 'block';
                }
                
                // Очищаем корзину
                if (window.cartFunctions) {
                    window.cartFunctions.clearCart();
                }
                
                // Обновляем счетчик
                if (typeof window.updateCartCounter === 'function') {
                    window.updateCartCounter();
                }
                
                // Сбрасываем форму
                orderForm.reset();
                
                showNotification('Заказ успешно сохранен в базе данных!', 'success');
            } else {
                showNotification(result.message, 'error');
            }
            
            submitBtn.value = originalText;
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
        });
    }
    
    // ====== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ======
    
    function updateUserInterface(user) {
        // Обновляем кнопку входа в шапке
        const authButton = document.getElementById('auth-button');
        if (authButton && user) {
            const loginIcon = authButton.querySelector('.login-icon');
            const loginText = authButton.querySelector('.sb-hidden-mobile');
            
            if (loginIcon) loginIcon.textContent = '✓';
            if (loginText) loginText.textContent = user.name;
            
            // Меняем обработчик на выход
            authButton.onclick = async function(e) {
                e.preventDefault();
                
                const result = await API.logout();
                if (result.success) {
                    localStorage.removeItem('user');
                    localStorage.removeItem('isLoggedIn');
                    
                    if (loginIcon) loginIcon.textContent = '👤';
                    if (loginText) loginText.textContent = 'Войти';
                    
                    showNotification('Вы вышли из системы', 'success');
                }
            };
        }
        
        // Разблокируем корзину
        document.querySelectorAll('.cart-link').forEach(link => {
            link.href = 'cart.html';
            link.style.cursor = 'pointer';
            link.title = '';
        });
    }
    
    function showNotification(message, type = 'success') {
        // Ваша существующая функция показа уведомлений
        // Добавьте параметр type для разных стилей уведомлений
    }
    
    function closeAuthModal() {
        const authModal = document.getElementById('auth-modal');
        if (authModal) {
            authModal.classList.remove('show');
        }
    }
    
    // ====== ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ ======
    
    document.addEventListener('DOMContentLoaded', function() {
        // Обновляем формы
        updateRegisterForm();
        updateLoginForm();
        updateOrderForm();
        
        // Проверяем авторизацию при загрузке
        const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
        const userData = JSON.parse(localStorage.getItem('user') || 'null');
        
        if (isLoggedIn && userData) {
            updateUserInterface(userData);
        }
    });
    
    // Экспортируем API для использования в других файлах
    window.FoodAPI = API;
    
})();
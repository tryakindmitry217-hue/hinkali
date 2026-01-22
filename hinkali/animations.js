// animations.js - Дополнительные анимации
class FoodDeliveryAnimations {
    constructor() {
        this.initParallaxEffect();
        this.initScrollAnimations();
    }
    
    // ====== ОСНОВНЫЕ АНИМАЦИИ ПРИ ПРОКРУТКЕ ======
    initScrollAnimations() {
        console.log('FoodDeliveryAnimations: Инициализация анимаций при прокрутке');
        
        // Функция проверки видимости элемента
        const isElementInViewport = (el) => {
            const rect = el.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 &&
                rect.bottom >= 0
            );
        };
        
        // Настройки для IntersectionObserver
        const observerOptions = {
            threshold: 0.05,
            rootMargin: '50px 0px -30px 0px'
        };
        
        // Создаем наблюдатель
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Добавляем класс для анимации
                    entry.target.classList.add('animated');
                    console.log('FoodDeliveryAnimations: Анимация добавлена для элемента', entry.target);
                    // Отключаем наблюдение после анимации для оптимизации
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Находим все элементы, которые нужно анимировать
        const elementsToAnimate = document.querySelectorAll(
            '.dish-card, .how-it-works, .order-form, #map'
        );
        
        console.log('FoodDeliveryAnimations: Найдено элементов для анимации:', elementsToAnimate.length);
        
        // Начинаем наблюдение за элементами
        elementsToAnimate.forEach(el => {
            // Проверяем, виден ли элемент сразу при загрузке
            if (isElementInViewport(el)) {
                el.classList.add('animated');
                console.log('FoodDeliveryAnimations: Элемент виден сразу, добавляем анимацию', el);
            } else {
                observer.observe(el);
            }
        });
    }
    
    // ====== ПАРАЛЛАКС-ЭФФЕКТ ======
    initParallaxEffect() {
        const parallaxElements = document.querySelectorAll('.hero, .popular-dishes');
        
        if (parallaxElements.length === 0) {
            return;
        }
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = 0.5;
                const yPos = -(scrolled * speed);
                element.style.backgroundPositionY = `${yPos}px`;
            });
        });
    }
    
    // ====== АНИМАЦИЯ ДОБАВЛЕНИЯ ТОВАРА В КОРЗИНУ ======
    static animateAddToCart(button, cartIcon) {
        console.log('FoodDeliveryAnimations: Анимация добавления в корзину');
        
        // Создаем летающий элемент
        const flyingItem = document.createElement('div');
        flyingItem.className = 'flying-item';
        flyingItem.style.position = 'fixed';
        flyingItem.style.zIndex = '10000';
        flyingItem.style.pointerEvents = 'none';
        flyingItem.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        flyingItem.style.borderRadius = '50%';
        flyingItem.style.background = '#ff6b35';
        
        const buttonRect = button.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();
        
        flyingItem.style.left = buttonRect.left + buttonRect.width / 2 - 10 + 'px';
        flyingItem.style.top = buttonRect.top + buttonRect.height / 2 - 10 + 'px';
        flyingItem.style.width = '20px';
        flyingItem.style.height = '20px';
        
        document.body.appendChild(flyingItem);
        
        // Анимация полета к корзине
        setTimeout(() => {
            flyingItem.style.left = cartRect.left + cartRect.width / 2 - 10 + 'px';
            flyingItem.style.top = cartRect.top + cartRect.height / 2 - 10 + 'px';
            flyingItem.style.width = '5px';
            flyingItem.style.height = '5px';
            flyingItem.style.opacity = '0';
        }, 10);
        
        // Удаляем элемент после анимации
        setTimeout(() => {
            if (flyingItem.parentNode) {
                flyingItem.remove();
            }
        }, 810);
        
        // Анимация счетчика корзины
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.classList.add('bounce');
            setTimeout(() => {
                cartCount.classList.remove('bounce');
            }, 500);
        }
    }
    
    // ====== УВЕДОМЛЕНИЯ ======
    static showNotification(message, type = 'success') {
        // Используем существующее уведомление
        const notification = document.getElementById('notification');
        if (!notification) return;
        
        // Определяем цвет в зависимости от типа
        const color = type === 'success' ? '#4CAF50' : 
                     type === 'error' ? '#ff3860' : '#000';
        
        // Обновляем уведомление
        notification.textContent = message;
        notification.style.backgroundColor = color;
        notification.style.borderColor = color === '#000' ? '#fff' : '#000';
        
        // Показываем уведомление
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Скрываем через 3 секунды
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    // ====== АНИМАЦИЯ КНОПОК ======
    static animateButton(button, type = 'pulse') {
        if (type === 'pulse') {
            button.classList.add('pulse-animation');
            setTimeout(() => {
                button.classList.remove('pulse-animation');
            }, 600);
        } else if (type === 'shake') {
            button.classList.add('shake-animation');
            setTimeout(() => {
                button.classList.remove('shake-animation');
            }, 600);
        }
    }
    
    // ====== ИНИЦИАЛИЗАЦИЯ ВСЕХ АНИМАЦИЙ ======
    initAllAnimations() {
        console.log('FoodDeliveryAnimations: Инициализация всех анимаций');
        
        // Инициализируем все анимации
        this.initScrollAnimations();
        this.initParallaxEffect();
        
        // Инициализируем анимации для интерактивных элементов
        this.initInteractiveAnimations();
    }
    
    // Инициализация анимаций для интерактивных элементов
    initInteractiveAnimations() {
        // Анимация кнопок при наведении
        const buttons = document.querySelectorAll('.btn, .hero-btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'scale(1.05)';
                button.style.transition = 'transform 0.3s ease';
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'scale(1)';
            });
            
            button.addEventListener('click', (e) => {
                FoodDeliveryAnimations.animateButton(button, 'pulse');
            });
        });
        
        // Анимация карточек блюд
        const dishCards = document.querySelectorAll('.dish-card');
        dishCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
                card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = 'none';
            });
        });
        
        // Анимация ссылок навигации
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('mouseenter', () => {
                link.style.transform = 'translateY(-2px)';
                link.style.transition = 'transform 0.3s ease';
            });
            
            link.addEventListener('mouseleave', () => {
                link.style.transform = 'translateY(0)';
            });
        });
    }
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM загружен, инициализация FoodDeliveryAnimations');
    
    // Создаем глобальный объект анимаций
    window.foodDeliveryAnimations = new FoodDeliveryAnimations();
    
    // Запускаем все анимации
    setTimeout(() => {
        window.foodDeliveryAnimations.initAllAnimations();
        console.log('Все анимации инициализированы');
    }, 100);
});

// Экспорт для использования в других модулях
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FoodDeliveryAnimations;
}
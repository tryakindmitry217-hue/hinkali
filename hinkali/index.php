<?php
// Добавьте это в НАЧАЛО файла index.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Хинкаль - Ресторан грузинской кухни</title>
    <meta name="description" content="Ресторан грузинской кухни 'Хинкаль'. Закажите грузинские блюда с доставкой на дом">
    <meta name="keywords" content="грузинская кухня, хинкали, хачапури, шашлык, доставка еды, ресторан">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="Хинкаль - Ресторан грузинской кухни">
    <meta property="og:description" content="Закажите грузинские блюда с доставкой на дом">
    <meta property="og:image" content="images/0ced8a2f-18b3-40a9-9905-484711090829.png">
    <meta name="twitter:title" content="Хинкаль - Ресторан грузинской кухни">
    <meta name="twitter:description" content="Закажите грузинские блюда с доставкой на дом">
    <meta name="twitter:image" content="images/0ced8a2f-18b3-40a9-9905-484711090829.png">
    <meta name="twitter:card" content="summary_large_image">
    
    <style>
        /* Общие стили */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            background: #fff;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #000;
            background: #fff;
            color: #000;
        }
        
        .btn:hover {
            background: #000;
            color: #fff;
        }
        
        .btn-primary {
            background: #000;
            color: #fff;
        }
        
        .btn-primary:hover {
            background: #fff;
            color: #000;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: #000;
        }
        
        /* Шапка */
        .header {
            background: #000;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .header-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
        }
        
        .logo {
            width: 150px;
            height: auto;
        }
        
        /* Навигация */
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .nav-link {
            color: #fff;
            font-size: 1rem;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-link:hover {
            color: #ccc;
        }
        
        .nav-link.active {
            color: #fff;
            font-weight: bold;
        }
        
        .cart-indicator {
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #fff;
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Бургер-меню */
        .burger {
            display: none;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 5px;
        }
        
        .burger-line {
            width: 25px;
            height: 3px;
            background: #fff;
            border-radius: 2px;
        }
        
        /* === ГЛАВНЫЙ БАННЕР === */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/1251546f-4037-4cbd-a4ef-acf178f9a238-629818.png');
            background-size: cover;
            background-position: center;
            color: #fff;
            text-align: center;
            padding: 150px 20px 100px;
            margin-top: 60px;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px;
            color: #ccc;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* СТИЛИ ДЛЯ КНОПОК В ГЛАВНОМ БАННЕРЕ */
        /* Белые кнопки с черным текстом */
        .hero-btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #fff; /* Белая рамка */
            background: #fff; /* Белый фон */
            color: #000; /* Черный текст */
            text-decoration: none;
            display: inline-block;
        }
        
        /* При наведении - инвертируем цвета */
        .hero-btn:hover {
            background: #000; /* Черный фон */
            color: #fff; /* Белый текст */
            border-color: #000; /* Черная рамка */
        }
        
        /* Секция "Как это работает" */
        .section {
            padding: 80px 20px;
            background: #fff;
        }
        
        .how-it-works {
            display: flex;
            align-items: center;
            gap: 50px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .how-it-works img {
            width: 400px;
            border-radius: 10px;
            border: 2px solid #000;
        }
        
        .steps {
            flex: 1;
        }
        
        .steps ol {
            padding-left: 20px;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #000;
        }
        
        .steps li {
            margin-bottom: 15px;
            padding-left: 10px;
        }
        
        /* Популярные блюда */
        .popular-dishes {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/38f82e11-6c54-408f-8446-a09c70427df0-623843.png');
            background-size: cover;
            background-position: center;
            color: #fff;
            text-align: center;
            padding: 80px 20px;
        }
        
        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 50px auto;
            max-width: 1200px;
        }
        
        .dish-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            border: 2px solid #000;
            transition: transform 0.3s, box-shadow 0.3s;
            color: #000;
        }
        
        .dish-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        
        .dish-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .dish-content {
            padding: 25px;
        }
        
        .dish-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .dish-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .dish-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dish-price {
            font-size: 1.8rem;
            color: #000;
            font-weight: 700;
        }
        
        /* Форма заказа */
        .order-form-section {
            background: #fff;
            color: #000;
            padding: 80px 20px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .order-form {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 40px;
            border-radius: 15px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #000;
            border-radius: 8px;
            background: #fff;
            color: #000;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        /* Карта */
        .map-section {
            background: #fff;
            padding: 80px 20px;
            text-align: center;
        }
        
        /* Контейнер для карты */
        #map {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            margin: 30px auto;
            border: 2px solid #000;
            overflow: hidden;
        }
        
        /* Стиль для серой Яндекс Карты */
        .ymaps-layers-pane {
            filter: grayscale(1); /* Делает карту черно-белой (серой) */
            -webkit-filter: grayscale(1);
        }
        
        /* Футер */
        .footer {
            background: #000;
            color: #fff;
            padding: 50px 20px;
            text-align: center;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .footer-link {
            color: #ccc;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: #fff;
        }
        
        .copyright {
            color: #999;
            font-size: 0.9rem;
        }
        
        /* Модальное окно авторизации */
        .auth-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            display: none;
        }
        
        .auth-modal.active {
            display: flex;
        }
        
        .auth-content {
            background: #fff;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            border: 2px solid #000;
            overflow: hidden;
        }
        
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid #000;
        }
        
        .auth-tab {
            flex: 1;
            padding: 15px;
            background: none;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: 600;
        }
        
        .auth-tab.active {
            background: #000;
            color: #fff;
        }
        
        .auth-form {
            padding: 30px;
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .auth-form h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #000;
        }
        
        .auth-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #000;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .auth-form button {
            width: 100%;
            margin-top: 10px;
        }
        
        .close-auth {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
        }
        
        /* Уведомления */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #000;
            color: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            border: 2px solid #fff;
            z-index: 1001;
            display: none;
        }
        
        .notification.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Анимации для элементов при скролле */
        .dish-card, .how-it-works, .order-form, #map {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .dish-card.animated, .how-it-works.animated, .order-form.animated, #map.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Анимация для счетчика корзины */
        .cart-count.bounce {
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }
        
        /* Анимация для кнопок */
        .pulse-animation {
            animation: pulse 0.6s ease;
        }
        
        .shake-animation {
            animation: shake 0.5s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        /* Загрузка */
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .how-it-works {
                flex-direction: column;
                text-align: center;
            }
            
            .how-it-works img {
                width: 100%;
                max-width: 400px;
            }
            
            .dishes-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                background: #000;
                flex-direction: column;
                padding: 20px;
                gap: 20px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .burger {
                display: flex;
            }
            
            .order-form {
                padding: 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            #map {
                height: 300px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .dishes-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Шапка -->
    <header class="header">
        <div class="container">
            <div class="header-wrapper">
                <a href="index.html">
                    <img src="images/0ced8a2f-18b3-40a9-9905-484711090829.png" alt="Хинкаль" class="logo">
                </a>
                
                <button class="burger">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>
                
                <nav class="nav-links">
                    <a href="index.html" class="nav-link active">Главная</a>
                    <a href="menu.html" class="nav-link">Меню</a>
                    <a href="contacts.html" class="nav-link">Контакты</a>
                    <a href="#" class="nav-link cart-indicator cart-link">
                        Корзина
                        <span class="cart-count">0</span>
                    </a>
                    <button class="btn auth-btn" id="authBtn">Войти</button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Главный баннер -->
    <section class="hero">
        <h1>Ресторан грузинской кухни "Хинкаль"</h1>
        <p>Насладитесь настоящим вкусом Грузии с доставкой на дом</p>
        <div class="hero-buttons">
            <a href="#menu" class="btn btn-primary hero-btn">Выбрать блюда</a>
            <a href="#how-it-works" class="btn hero-btn">Как это работает</a>
        </div>
    </section>

    <!-- Как это работает -->
    <section id="how-it-works" class="section">
        <h2 class="section-title">Как заказать доставку</h2>
        <div class="how-it-works">
            <img src="images/a2d94406-0962-430a-bb3b-a71ee48ca7bf.png" alt="Процесс заказа">
            <div class="steps">
                <ol>
                    <li>Выберите блюда из нашего меню</li>
                    <li>Добавьте понравившиеся позиции в корзину</li>
                    <li>Укажите адрес доставки и время</li>
                    <li>Оплатите заказ онлайн или при получении</li>
                    <li>Получите горячие грузинские блюда в удобное время</li>
                </ol>
                <p style="margin-top: 20px; font-weight: 600;">
                    Среднее время доставки: 30-60 минут
                </p>
            </div>
        </div>
    </section>

    <!-- Популярные блюда -->
    <section id="menu" class="popular-dishes">
        <h2 class="section-title">Популярные блюда</h2>
        <div class="dishes-grid">
            <!-- Хинкали -->
            <a href="hinkali.html" class="dish-card">
                <img src="images/hinkali.jpg" alt="Хинкали" class="dish-image">
                <div class="dish-content">
                    <h3 class="dish-title">Хинкали</h3>
                    <p class="dish-description">Свинина и говядина, специи, бульон. Классическое грузинское блюдо с сочной начинкой.</p>
                    <div class="dish-footer">
                        <div class="dish-price">350₽</div>
                    </div>
                </div>
            </a>
            
            <!-- Хачапури -->
            <a href="hachapuri.html" class="dish-card">
                <img src="images/hachapuri.jpg" alt="Хачапури" class="dish-image">
                <div class="dish-content">
                    <h3 class="dish-title">Хачапури по-аджарски</h3>
                    <p class="dish-description">Сыр сулугуни, яйцо, масло. Традиционное грузинское блюдо в форме лодочки.</p>
                    <div class="dish-footer">
                        <div class="dish-price">450₽</div>
                    </div>
                </div>
            </a>
            
            <!-- Шашлык из свинины -->
            <a href="shashlik-svinina.html" class="dish-card">
                <img src="images/shashlik-svinina.jpg" alt="Шашлык из свинины" class="dish-image">
                <div class="dish-content">
                    <h3 class="dish-title">Шашлык из свинины</h3>
                    <p class="dish-description">Свиная шея, лук, специи, зелень. Нежный и сочный шашлык по-грузински.</p>
                    <div class="dish-footer">
                        <div class="dish-price">550₽</div>
                    </div>
                </div>
            </a>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="menu.html" class="btn hero-btn">Смотреть все меню</a>
        </div>
    </section>

    <!-- Форма заказа -->
    <section id="order-form" class="order-form-section">
        <h2 class="section-title">Оформление заказа</h2>
        <div class="order-form">
            <form id="checkout-form">
                <div class="form-group">
                    <label for="name">Ваше имя *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Адрес доставки *</label>
                    <input type="text" id="address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Желаемое время доставки *</label>
                    <input type="text" id="time" name="time" required>
                </div>
                
                <div class="form-group">
                    <label for="comment">Комментарий к заказу</label>
                    <textarea id="comment" name="comment"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Оформить заказ</button>
            </form>
        </div>
    </section>

    <!-- Секция с Яндекс Картой -->
    <section class="map-section">
        <h2 class="section-title">Расположение ресторана на карте</h2>
        <!-- Контейнер для Яндекс Карты -->
        <div id="map"></div>
        <p style="margin-top: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Ресторан грузинской кухни "Хинкаль" — приходите к нам или заказывайте доставку!
        </p>
    </section>

    <!-- Футер -->
    <footer class="footer">
        <div class="container">
            <div class="footer-links">
                <a href="index.html" class="footer-link">Главная</a>
                <a href="menu.html" class="footer-link">Меню</a>
                <a href="contacts.html" class="footer-link">Контакты</a>
                <a href="#" class="footer-link">Политика конфиденциальности</a>
                <a href="#" class="footer-link">Условия использования</a>
            </div>
            <div class="copyright">
                © 2023 Хинкаль. Все права защищены.
            </div>
        </div>
    </footer>

    <!-- Модальное окно авторизации -->
    <div class="auth-modal" id="authModal">
        <button class="close-auth" id="closeAuth">×</button>
        <div class="auth-content">
            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login">Вход</button>
                <button class="auth-tab" data-tab="register">Регистрация</button>
            </div>
            
            <!-- Форма входа -->
            <form class="auth-form active" id="loginForm">
                <h3>Вход в аккаунт</h3>
                <input type="email" id="loginEmail" placeholder="Email" required>
                <input type="password" id="loginPassword" placeholder="Пароль" required>
                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
            
            <!-- Форма регистрации -->
            <form class="auth-form" id="registerForm">
                <h3>Создать аккаунт</h3>
                <input type="text" id="registerName" placeholder="Имя" required>
                <input type="email" id="registerEmail" placeholder="Email" required>
                <input type="tel" id="registerPhone" placeholder="Телефон" required>
                <input type="password" id="registerPassword" placeholder="Пароль" required>
                <input type="password" id="registerConfirm" placeholder="Подтвердите пароль" required>
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </form>
        </div>
    </div>

    <!-- Уведомление -->
    <div class="notification" id="notification"></div>

    <!-- ПОДКЛЮЧЕНИЕ API ЯНДЕКС КАРТ -->
    <!-- Замените "ВАШ_API_КЛЮЧ" на ваш ключ или используйте тестовый -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=ваш_api_ключ&lang=ru_RU" type="text/javascript"></script>

    <!-- ПОДКЛЮЧЕНИЕ АНИМАЦИЙ -->
    <script src="animations.js"></script>

    <script>
        // ==================== ЯНДЕКС КАРТА ====================
        // Инициализация карты после загрузки API
        ymaps.ready(initMap);

        function initMap() {
            // Координаты ресторана (например, центр Москвы)
            var restaurantCoords = [55.751574, 37.618856];

            // Создание карты
            var myMap = new ymaps.Map("map", {
                center: restaurantCoords,
                zoom: 16,
                controls: ['zoomControl', 'fullscreenControl'] // Добавляем элементы управления
            });

            // Создание метки
            var myPlacemark = new ymaps.Placemark(restaurantCoords, {
                balloonContentHeader: 'Хинкаль',
                balloonContentBody: 'Ресторан грузинской кухни.<br>Время работы: 10:00 - 23:00.',
                balloonContentFooter: '<strong>Заказ столика: +7 (495) 123-45-67</strong>',
                hintContent: 'Ресторан "Хинкаль"'
            }, {
                preset: 'islands#redFoodIcon' // Иконка метки
            });

            // Добавление метки на карту
            myMap.geoObjects.add(myPlacemark);

            // Карта уже стилизована в серый цвет через CSS
        }

        // ==================== ОСНОВНОЙ СКРИПТ С ИНТЕГРАЦИЕЙ БД ====================
        
        // Бургер-меню
        const burger = document.querySelector('.burger');
        const navLinks = document.querySelector('.nav-links');
        
        burger.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
        
        // Закрытие меню при клике на ссылку
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
        
        // Корзина
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        function updateCartCounter() {
            const cartCount = document.querySelector('.cart-count');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (cartCount) {
                if (totalItems > 0) {
                    cartCount.textContent = totalItems;
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }
            }
        }
        
        // Инициализация счетчика корзины
        updateCartCounter();
        
        // Авторизация
        const authModal = document.getElementById('authModal');
        const authBtn = document.getElementById('authBtn');
        const closeAuth = document.getElementById('closeAuth');
        const authTabs = document.querySelectorAll('.auth-tab');
        const authForms = document.querySelectorAll('.auth-form');
        const notification = document.getElementById('notification');
        
        let currentUser = JSON.parse(localStorage.getItem('currentUser'));
        
        // Функция для показа уведомлений
        function showNotification(message, type = 'info') {
            notification.textContent = message;
            
            // Устанавливаем цвет в зависимости от типа
            if (type === 'success') {
                notification.style.backgroundColor = '#4CAF50';
                notification.style.borderColor = '#4CAF50';
            } else if (type === 'error') {
                notification.style.backgroundColor = '#ff3860';
                notification.style.borderColor = '#ff3860';
            } else {
                notification.style.backgroundColor = '#000';
                notification.style.borderColor = '#fff';
            }
            
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Обработка клика по ссылке "Корзина"
        document.querySelectorAll('.cart-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (currentUser) {
                    window.location.href = 'cart.html';
                } else {
                    authModal.classList.add('active');
                    showNotification('Для доступа к корзине необходимо войти в систему', 'error');
                }
            });
        });
        
        // Открытие модального окна авторизации по кнопке "Войти"
        authBtn.addEventListener('click', function() {
            if (currentUser) {
                if (confirm(`Вы вошли как ${currentUser.name}. Выйти из аккаунта?`)) {
                    // Отправляем запрос на выход
                    fetch('auth_handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=logout'
                    })
                    .then(response => response.json())
                    .then(data => {
                        localStorage.removeItem('currentUser');
                        currentUser = null;
                        authBtn.textContent = 'Войти';
                        showNotification('Вы вышли из аккаунта', 'success');
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        showNotification('Ошибка соединения с сервером', 'error');
                    });
                }
            } else {
                authModal.classList.add('active');
            }
        });
        
        // Закрытие модального окна
        closeAuth.addEventListener('click', function() {
            authModal.classList.remove('active');
        });
        
        // Закрытие по клику вне окна
        authModal.addEventListener('click', function(e) {
            if (e.target === authModal) {
                authModal.classList.remove('active');
            }
        });
        
        // Переключение между вкладками
        authTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                authTabs.forEach(t => t.classList.remove('active'));
                authForms.forEach(f => f.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(tabName + 'Form').classList.add('active');
            });
        });
        
        // ==================== ИНТЕГРАЦИЯ С БАЗОЙ ДАННЫХ ====================
        
        // Обработка формы входа
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const submitBtn = this.querySelector('button');
            
            // Валидация
            if (!email || !password) {
                showNotification('Заполните все поля', 'error');
                return;
            }
            
            // Отправка данных на сервер
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);
            
            // Показываем состояние загрузки
            submitBtn.textContent = 'Вход...';
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            fetch('auth_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Сохраняем данные пользователя
                    currentUser = data.user;
                    localStorage.setItem('currentUser', JSON.stringify(currentUser));
                    
                    // Обновляем интерфейс
                    authModal.classList.remove('active');
                    authBtn.textContent = 'Выйти';
                    showNotification(data.message, 'success');
                    
                    // Очищаем форму
                    document.getElementById('loginForm').reset();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('Ошибка соединения с сервером', 'error');
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.textContent = 'Войти';
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
        
        // Обработка формы регистрации
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const phone = document.getElementById('registerPhone').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirm').value;
            const submitBtn = this.querySelector('button');
            
            // Валидация
            const errors = [];
            if (!name) errors.push('Введите имя');
            if (!email) errors.push('Введите email');
            if (!phone) errors.push('Введите телефон');
            if (!password) errors.push('Введите пароль');
            if (password !== confirmPassword) errors.push('Пароли не совпадают');
            
            if (errors.length > 0) {
                showNotification(errors.join('<br>'), 'error');
                return;
            }
            
            // Отправка данных на сервер
            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('password', password);
            formData.append('confirm_password', confirmPassword);
            
            // Показываем состояние загрузки
            submitBtn.textContent = 'Регистрация...';
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            fetch('auth_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Сохраняем данные пользователя
                    currentUser = data.user;
                    localStorage.setItem('currentUser', JSON.stringify(currentUser));
                    
                    // Обновляем интерфейс
                    authModal.classList.remove('active');
                    authBtn.textContent = 'Выйти';
                    showNotification(data.message, 'success');
                    
                    // Очищаем форму
                    document.getElementById('registerForm').reset();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('Ошибка соединения с сервером', 'error');
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.textContent = 'Зарегистрироваться';
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
        
        // Обработка формы заказа
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button');
            
            // Проверка авторизации
            if (!currentUser) {
                showNotification('Для оформления заказа необходимо войти в систему', 'error');
                authModal.classList.add('active');
                return;
            }
            
            // Проверка корзины
            if (cart.length === 0) {
                showNotification('Добавьте товары в корзину перед оформлением заказа', 'error');
                return;
            }
            
            // Собираем данные формы
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                time: document.getElementById('time').value,
                comment: document.getElementById('comment').value,
                cart: JSON.stringify(cart),
                user_id: currentUser.id
            };
            
            // Валидация
            const required = ['name', 'phone', 'address', 'time'];
            const errors = [];
            
            required.forEach(field => {
                if (!formData[field]) {
                    errors.push(`Поле "${field}" обязательно`);
                }
            });
            
            if (errors.length > 0) {
                showNotification(errors.join('<br>'), 'error');
                return;
            }
            
            // Отправка заказа на сервер
            const data = new FormData();
            for (const key in formData) {
                data.append(key, formData[key]);
            }
            
            // Показываем состояние загрузки
            submitBtn.textContent = 'Отправка...';
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            fetch('order_handler.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Очищаем корзину
                    cart = [];
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartCounter();
                    
                    // Показываем успешное сообщение
                    showNotification('Заказ успешно оформлен! Номер заказа: ' + result.order.number, 'success');
                    
                    // Очищаем форму
                    document.getElementById('checkout-form').reset();
                } else {
                    showNotification(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('Ошибка соединения с сервером', 'error');
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.textContent = 'Оформить заказ';
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
        
        // Проверка авторизации при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            if (currentUser) {
                authBtn.textContent = 'Выйти';
            }
            
            // Загружаем данные корзины из localStorage
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                try {
                    cart = JSON.parse(savedCart);
                    updateCartCounter();
                } catch (e) {
                    console.error('Ошибка при загрузке корзины:', e);
                    cart = [];
                }
            }
        });
        
        // Функция для добавления товара в корзину (можно вызывать из других скриптов)
        window.addToCart = function(product) {
            // Поиск товара в корзине
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    image: product.image
                });
            }
            
            // Сохраняем в localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Обновляем счетчик
            updateCartCounter();
            
            // Показываем уведомление
            showNotification('Товар добавлен в корзину', 'success');
            
            // Запускаем анимацию
            if (window.foodDeliveryAnimations) {
                const addToCartBtn = document.querySelector(`[data-product-id="${product.id}"]`);
                const cartIcon = document.querySelector('.cart-indicator');
                if (addToCartBtn && cartIcon) {
                    window.foodDeliveryAnimations.animateAddToCart(addToCartBtn, cartIcon);
                }
            }
        };
        
        // Функция для удаления товара из корзины
        window.removeFromCart = function(productId) {
            cart = cart.filter(item => item.id !== productId);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCounter();
            showNotification('Товар удален из корзины', 'success');
        };
        
        // Функция для изменения количества товара
        window.updateCartItemQuantity = function(productId, quantity) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                if (quantity <= 0) {
                    window.removeFromCart(productId);
                } else {
                    item.quantity = quantity;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartCounter();
                }
            }
        };
        
        // Функция для получения общей суммы корзины
        window.getCartTotal = function() {
            return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        };
        
        // Экспортируем функции для использования в других файлах
        window.cartFunctions = {
            getCart: () => cart,
            clearCart: () => {
                cart = [];
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCounter();
            },
            getTotal: window.getCartTotal
        };
    </script>
</body>
</html>
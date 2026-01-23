<?php
// auth_handler.php - Обработчик авторизации и регистрации для PostgreSQL
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // В production установить 0

require_once 'config.php';

// Функция для отправки JSON ответа
function sendAuthResponse($success, $message, $user = null) {
    http_response_code($success ? 200 : 400);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'user' => $user
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Получаем действие
    $action = $_POST['action'] ?? '';
    
    // Логируем запрос для отладки
    if (DEBUG_MODE) {
        error_log("Auth Action: " . $action . " | POST: " . json_encode($_POST));
    }
    
    if ($action === 'login') {
        // ==================== ОБРАБОТКА ВХОДА ====================
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            sendAuthResponse(false, 'Заполните все поля');
        }
        
        // Проверяем email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendAuthResponse(false, 'Некорректный email');
        }
        
        // Поиск пользователя в PostgreSQL
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            "SELECT id, username, email, password, full_name, phone 
             FROM users 
             WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            sendAuthResponse(false, 'Пользователь не найден');
        }
        
        if (!password_verify($password, $user['password'])) {
            sendAuthResponse(false, 'Неверный пароль');
        }
        
        // Обновляем время последнего входа
        $stmt = $pdo->prepare(
            "UPDATE users 
             SET last_login = CURRENT_TIMESTAMP 
             WHERE id = ?"
        );
        $stmt->execute([$user['id']]);
        
        // Подготавливаем данные пользователя для клиента
        $userData = [
            'id' => (int)$user['id'],
            'name' => $user['full_name'] ?: $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ];
        
        sendAuthResponse(true, 'Вход выполнен успешно', $userData);
        
    } elseif ($action === 'register') {
        // ==================== ОБРАБОТКА РЕГИСТРАЦИИ ====================
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Валидация
        $errors = [];
        
        if (empty($name)) $errors[] = 'Введите имя';
        if (empty($email)) $errors[] = 'Введите email';
        if (empty($phone)) $errors[] = 'Введите телефон';
        if (empty($password)) $errors[] = 'Введите пароль';
        
        if ($password !== $confirm_password) {
            $errors[] = 'Пароли не совпадают';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некорректный email';
        }
        
        if (strlen($password) < 6) {
            $errors[] = 'Пароль должен содержать минимум 6 символов';
        }
        
        // Проверка существования email
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Пользователь с таким email уже существует';
        }
        
        if (!empty($errors)) {
            sendAuthResponse(false, implode('. ', $errors));
        }
        
        // Создание пользователя
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Для PostgreSQL используем RETURNING чтобы получить ID новой записи
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, full_name, phone, created_at) 
             VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP) 
             RETURNING id, username, email, full_name, phone"
        );
        
        $stmt->execute([$email, $email, $hashed_password, $name, $phone]);
        
        // Получаем данные новой записи напрямую из результата запроса
        $newUser = $stmt->fetch();
        
        $userData = [
            'id' => (int)$newUser['id'],
            'name' => $newUser['full_name'] ?: $newUser['username'],
            'email' => $newUser['email'],
            'phone' => $newUser['phone']
        ];
        
        sendAuthResponse(true, 'Регистрация успешна', $userData);
        
    } elseif ($action === 'logout') {
        // Обработка выхода
        sendAuthResponse(true, 'Вы вышли из системы');
        
    } else {
        sendAuthResponse(false, 'Неизвестное действие');
    }
    
} catch (PDOException $e) {
    error_log("Auth Handler PDO Error: " . $e->getMessage());
    sendAuthResponse(false, 'Ошибка базы данных: ' . (DEBUG_MODE ? $e->getMessage() : ''));
    
} catch (Exception $e) {
    error_log("Auth Handler General Error: " . $e->getMessage());
    sendAuthResponse(false, 'Произошла ошибка: ' . (DEBUG_MODE ? $e->getMessage() : ''));
}
?>

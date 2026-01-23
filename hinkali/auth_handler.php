<?php
// auth_handler.php - Обработчик авторизации и регистрации для PostgreSQL
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Функция для отправки JSON ответа
function sendAuthResponse($success, $message, $user = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'user' => $user
    ]);
    exit;
}

try {
    // Получаем действие
    $action = $_POST['action'] ?? '';
    
    // Логируем запрос для отладки
    if (DEBUG_MODE) {
        error_log("Auth Action: " . $action);
    }
    
    if ($action === 'login') {
        // ==================== ОБРАБОТКА ВХОДА ====================
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            sendAuthResponse(false, 'Заполните все поля');
        }
        
        // Поиск пользователя в PostgreSQL
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            "SELECT id, username, email, password, full_name, phone FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Обновляем время последнего входа
            $stmt = $pdo->prepare(
                "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?"
            );
            $stmt->execute([$user['id']]);
            
            // Подготавливаем данные пользователя для клиента
            $userData = [
                'id' => $user['id'],
                'name' => $user['full_name'] ?: $user['username'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ];
            
            sendAuthResponse(true, 'Вход выполнен успешно', $userData);
        } else {
            sendAuthResponse(false, 'Неверный email или пароль');
        }
        
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
        
        // Проверка существования email
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Пользователь с таким email уже существует';
        }
        
        if (!empty($errors)) {
            sendAuthResponse(false, implode('<br>', $errors));
        }
        
        // Создание пользователя
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Для PostgreSQL используем RETURNING чтобы получить ID новой записи
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, full_name, phone, created_at) 
             VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP) RETURNING id"
        );
        
        $stmt->execute([$email, $email, $hashed_password, $name, $phone]);
        
        // Получаем ID новой записи напрямую из результата запроса
        $result = $stmt->fetch();
        $user_id = $result['id'];
        
        // Получаем созданного пользователя
        $stmt = $pdo->prepare(
            "SELECT id, username, email, full_name, phone FROM users WHERE id = ?"
        );
        $stmt->execute([$user_id]);
        $newUser = $stmt->fetch();
        
        $userData = [
            'id' => $newUser['id'],
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
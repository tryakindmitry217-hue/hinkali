<?php
// auth_handler.php - Обработчик авторизации и регистрации
require_once 'config.php';

header('Content-Type: application/json');

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
    
    if ($action === 'login') {
        // Обработка входа
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            sendAuthResponse(false, 'Заполните все поля');
        }
        
        // Поиск пользователя
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
        // Обработка регистрации
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
        
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([$email, $email, $hashed_password, $name, $phone]);
        
        $user_id = $pdo->lastInsertId();
        
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
    error_log("Auth Handler Error: " . $e->getMessage());
    sendAuthResponse(false, 'Ошибка базы данных');
    
} catch (Exception $e) {
    error_log("Auth Handler Error: " . $e->getMessage());
    sendAuthResponse(false, 'Произошла ошибка');
}
?>
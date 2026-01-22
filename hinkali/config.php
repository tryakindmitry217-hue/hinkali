<?php
// config.php - Конфигурация базы данных

// Настройки отладки (включить на локальном сервере)
define('DEBUG_MODE', true);

// Параметры подключения
define('DB_HOST', 'localhost');
define('DB_NAME', 'hinkali_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Функция для логирования ошибок
function logError($message) {
    if (DEBUG_MODE) {
        error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, 'error.log');
    }
}

// Создание подключения
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        logError("Connection Error: " . $e->getMessage());
        
        // В режиме отладки показываем ошибку
        if (DEBUG_MODE) {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к базе данных: ' . $e->getMessage()
            ]));
        } else {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.'
            ]));
        }
    }
}

// Хэлпер для выполнения запросов
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        logError("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

// Получение одной записи
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

// Получение всех записей
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

// Проверка существования записи
function recordExists($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() > 0;
}

// Получение последнего ID
function getLastInsertId() {
    $pdo = getDBConnection();
    return $pdo->lastInsertId();
}

// Экранирование строки для безопасности
function escapeString($string) {
    $pdo = getDBConnection();
    return $pdo->quote($string);
}
?>
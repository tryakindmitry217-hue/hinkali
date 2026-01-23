<?php
// config.php - Конфигурация базы данных для PostgreSQL на Render
// ==================== НАСТРОЙКИ ====================

// Режим отладки: true - показывать ошибки, false - скрывать (для продакшена)
define('DEBUG_MODE', true);

// ==================== ДАННЫЕ ПОДКЛЮЧЕНИЯ К БАЗЕ ====================
// ЗАМЕНИТЕ ЭТИ ЗНАЧЕНИЯ НА ВАШИ ИЗ RENDER (External Connection)
define('DB_HOST', 'dpg-d5pa7nc9c44c738aor3g-a.oregon-postgres.render.com'); // Внешний хост
define('DB_NAME', 'hinkali_db');
define('DB_USER', 'hinkali_db_user');
define('DB_PASS', 'rQWOai98ha2maFbcZqZMngio7AFekYBD'); // Ваш пароль
define('DB_PORT', '5432');

// ==================== ФУНКЦИЯ ПОДКЛЮЧЕНИЯ К БАЗЕ ====================
function getDBConnection() {
    try {
        // Формируем DSN строку для PostgreSQL
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";";
        
        // Параметры подключения PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Генерировать исключения при ошибках
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Возвращать ассоциативные массивы
            PDO::ATTR_EMULATE_PREPARES => false,                   // Использовать нативные подготовленные выражения
            // КРИТИЧЕСКИ ВАЖНО для Render: включаем SSL
            PDO::PGSQL_ATTR_SSL_MODE => PDO::PGSQL_SSL_REQUIRE
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Устанавливаем кодировку UTF-8
        $pdo->exec("SET NAMES 'UTF8'");
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Логируем ошибку для отладки
        error_log("DATABASE CONNECTION ERROR [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage());
        
        // В зависимости от режима отладки показываем разную информацию
        if (DEBUG_MODE) {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к базе данных',
                'error_details' => $e->getMessage(),
                'debug_info' => [
                    'host' => DB_HOST,
                    'dbname' => DB_NAME,
                    'user' => DB_USER,
                    'port' => DB_PORT
                ]
            ]));
        } else {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к серверу. Пожалуйста, попробуйте позже.'
            ]));
        }
    }
}

// ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================

/**
 * Логирование ошибок в файл
 */
function logError($message) {
    if (DEBUG_MODE) {
        $log_file = __DIR__ . '/error.log';
        $log_message = date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    }
}

/**
 * Выполнение SQL запроса с параметрами
 */
function executeQuery($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        logError("QUERY ERROR: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

/**
 * Получение одной записи
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Получение всех записей
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Проверка существования записи
 */
function recordExists($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() > 0;
}

/**
 * Получение ID последней вставленной записи
 */
function getLastInsertId($pdo) {
    // Для PostgreSQL
    $stmt = $pdo->query("SELECT LASTVAL()");
    return (int)$stmt->fetchColumn();
}

/**
 * Экранирование строки (лучше использовать подготовленные выражения!)
 */
function escapeString($string) {
    $pdo = getDBConnection();
    return $pdo->quote($string);
}

/**
 * Простая функция для проверки подключения
 */
function testDatabaseConnection() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        return $result && $result['test'] == 1;
    } catch (Exception $e) {
        return false;
    }
}

// ==================== АВТОПРОВЕРКА ПРИ ЗАГРУЗКЕ (опционально) ====================
// Раскомментируйте для автоматической проверки подключения
/*
if (DEBUG_MODE && php_sapi_name() !== 'cli') {
    if (!testDatabaseConnection()) {
        error_log("AUTO-CHECK: Database connection test failed");
    }
}
*/
?>

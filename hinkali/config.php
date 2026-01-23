<?php
// config.php - Конфигурация базы данных для PostgreSQL на Render
// ВСЕ НАСТРОЙКИ БЕРУТСЯ ИЗ ВАШЕГО External Database URL:
// postgresql://hinkali_db_user:rQWOai98ha2maFbcZqZMngio7AFekYBD@dpg-d5pa7nc9c44c738aor3g-a.oregon-postgres.render.com/hinkali_db

// Настройки отладки (включить для поиска ошибок)
define('DEBUG_MODE', true);

// ==================== ПАРАМЕТРЫ ИЗ ВАШЕГО RENDER DASHBOARD ====================
define('DB_HOST', 'dpg-d5pa7nc9c44c738aor3g-a.oregon-postgres.render.com'); // Часть после @
define('DB_NAME', 'hinkali_db');                    // Часть после последнего /
define('DB_USER', 'hinkali_db_user');               // Часть после ://
define('DB_PASS', 'rQWOai98ha2maFbcZqZMngio7AFekYBD'); // Часть между : и @

// ==================== ФУНКЦИЯ ПОДКЛЮЧЕНИЯ ====================
function getDBConnection() {
    try {
        // Формируем строку DSN для PostgreSQL
        $dsn = "pgsql:host=" . DB_HOST . ";port=5432;dbname=" . DB_NAME . ";";
        
        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                // КРИТИЧЕСКИ ВАЖНО: включаем SSL для Render
                PDO::PGSQL_ATTR_SSL_MODE => PDO::PGSQL_SSL_REQUIRE
            ]
        );
        
        // Устанавливаем кодировку для PostgreSQL
        $pdo->exec("SET NAMES 'UTF8'");
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Логируем ошибку и возвращаем понятное сообщение
        error_log("DATABASE CONNECTION ERROR: " . $e->getMessage());
        
        // В режиме отладки показываем детали
        if (DEBUG_MODE) {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к базе данных: ' . $e->getMessage(),
                'details' => [
                    'host' => DB_HOST,
                    'dbname' => DB_NAME,
                    'user' => DB_USER,
                    'ssl_mode' => 'PGSQL_SSL_REQUIRE'
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
function logError($message) {
    if (DEBUG_MODE) {
        $logMessage = date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
        // Записываем в файл error.log в той же папке
        file_put_contents('error.log', $logMessage, FILE_APPEND);
    }
}

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

function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

function recordExists($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() > 0;
}

function getLastInsertId($pdo) {
    // Для PostgreSQL нужно получить ID из последовательности
    $stmt = $pdo->query("SELECT LASTVAL()");
    return $stmt->fetchColumn();
}

// ==================== АВТО-ПРОВЕРКА ПРИ ПОДКЛЮЧЕНИИ ====================
// Убрать после отладки
if (DEBUG_MODE && php_sapi_name() !== 'cli') {
    try {
        $test_pdo = getDBConnection();
        error_log("AUTO-CHECK: Database connection successful to " . DB_HOST);
    } catch (PDOException $e) {
        error_log("AUTO-CHECK FAILED: " . $e->getMessage());
    }
}
?>

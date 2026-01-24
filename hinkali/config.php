<?php
// config.php - Конфигурация базы данных для PostgreSQL на Render

// Настройки отладки (включить для поиска ошибок)
define('DEBUG_MODE', true);

// ==================== ПАРАМЕТРЫ ОТ ВАШЕЙ БАЗЫ RENDER ====================
// Вставьте значения из раздела "Connections" вашей базы данных
define('DB_HOST', 'dpg-d5q83t4oud1c73e0arl0-a.oregon-postgres.render.com'); // ПОЛНОЕ имя хоста
define('DB_NAME', 'hinkali_db_q5k8'); // Имя базы из раздела Connections
define('DB_USER', 'hinkali_db_q5k8_user'); // Пользователь из раздела Connections
define('DB_PASS', 'YpTqZSxLN2O6HlW63lL6FdLSz0t5eKP7'); // Ваш пароль из Render

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
                'details' => 'Проверьте параметры в config.php'
            ], JSON_UNESCAPED_UNICODE));
        } else {
            die(json_encode([
                'success' => false,
                'message' => 'Ошибка подключения к серверу. Пожалуйста, попробуйте позже.'
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}

// ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================
function logError($message) {
    if (DEBUG_MODE) {
        $logFile = dirname(__FILE__) . '/error.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
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
    return $stmt->fetch() !== false;
}

// Функция для создания таблиц если они не существуют
function createTablesIfNotExist() {
    try {
        $pdo = getDBConnection();
        
        // Таблица users
        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(200),
            phone VARCHAR(20),
            last_login TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sqlUsers);
        
        // Таблица orders
        $sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
            id SERIAL PRIMARY KEY,
            user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
            customer_name VARCHAR(200) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            address TEXT NOT NULL,
            delivery_time VARCHAR(50) NOT NULL,
            comment TEXT,
            total_amount DECIMAL(10,2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sqlOrders);
        
        // Создаем индексы
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
            "CREATE INDEX IF NOT EXISTS idx_users_phone ON users(phone)",
            "CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id)",
            "CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status)",
            "CREATE INDEX IF NOT EXISTS idx_orders_created_at ON orders(created_at DESC)"
        ];
        
        foreach ($indexes as $sql) {
            $pdo->exec($sql);
        }
        
        logError("Tables created/verified successfully");
        return true;
        
    } catch (PDOException $e) {
        logError("Table creation error: " . $e->getMessage());
        return false;
    }
}

// Автоматическое создание таблиц при первом подключении
// createTablesIfNotExist();
?>

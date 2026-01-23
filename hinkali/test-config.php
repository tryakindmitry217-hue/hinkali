<?php
require_once 'config.php';
echo "<pre>";

if (testDatabaseConnection()) {
    echo "✅ Подключение к базе успешно!\n\n";
    
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    echo "Таблицы в базе:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
} else {
    echo "❌ Ошибка подключения\n";
    echo "Проверьте:\n";
    echo "1. Параметры в config.php\n";
    echo "2. Наличие драйвера pgsql на сервере\n";
    echo "3. Настройки сети в Render (раздел Networking базы данных)\n";
}
?>
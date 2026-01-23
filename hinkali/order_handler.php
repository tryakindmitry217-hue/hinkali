<?php
// order_handler.php - Обработчик заказов для PostgreSQL
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

function sendJsonResponse($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Проверяем метод запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Неправильный метод запроса');
    }
    
    // Получение данных из формы
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $cart = $_POST['cart'] ?? '[]';
    $user_id = $_POST['user_id'] ?? 0;
    
    // Логируем полученные данные для отладки
    if (DEBUG_MODE) {
        error_log("Order Data - Name: $name, Phone: $phone, UserID: $user_id");
    }
    
    // Валидация
    $errors = [];
    if (empty($name)) $errors[] = 'Введите имя';
    if (empty($phone)) $errors[] = 'Введите телефон';
    if (empty($address)) $errors[] = 'Введите адрес';
    if (empty($time)) $errors[] = 'Введите время доставки';
    
    // Проверяем корзину
    $cart_items = json_decode($cart, true);
    if (!$cart_items || !is_array($cart_items) || count($cart_items) === 0) {
        $errors[] = 'Корзина пуста';
    }
    
    if (!empty($errors)) {
        sendJsonResponse(false, implode('<br>', $errors));
    }
    
    // Валидация телефона
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    if (strlen($phone) < 10) {
        sendJsonResponse(false, 'Некорректный номер телефона');
    }
    
    // Расчет суммы заказа
    $total_amount = 0;
    $items_details = [];
    
    foreach ($cart_items as $item) {
        $price = floatval($item['price'] ?? 0);
        $quantity = intval($item['quantity'] ?? 1);
        $item_total = $price * $quantity;
        $total_amount += $item_total;
        
        $items_details[] = [
            'name' => $item['name'] ?? 'Товар',
            'quantity' => $quantity,
            'price' => $price,
            'total' => $item_total
        ];
    }
    
    // Добавляем стоимость доставки
    $delivery_fee = ($total_amount >= 1000) ? 0 : 200;
    $total_amount += $delivery_fee;
    
    // Подключаемся к базе данных
    $pdo = getDBConnection();
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    try {
        // Сохраняем заказ в базу данных
        // Для PostgreSQL используем RETURNING чтобы получить ID новой записи
        $stmt = $pdo->prepare(
            "INSERT INTO orders (user_id, customer_name, phone, address, delivery_time, comment, total_amount, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP) RETURNING id"
        );
        
        // Если user_id = 0, устанавливаем NULL для внешнего ключа
        $user_id_for_db = ($user_id > 0) ? $user_id : null;
        
        $stmt->execute([
            $user_id_for_db,
            $name,
            $phone,
            $address,
            $time,
            $comment,
            $total_amount
        ]);
        
        // Получаем ID новой записи напрямую из результата запроса
        $result = $stmt->fetch();
        $order_id = $result['id'];
        
        // Сохраняем детали заказа как JSON
        $order_details = json_encode([
            'items' => $items_details,
            'delivery_fee' => $delivery_fee,
            'subtotal' => $total_amount - $delivery_fee,
            'total' => $total_amount
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        // Обновляем комментарий с деталями заказа
        $updated_comment = ($comment ? $comment . "\n\n" : "") . "Детали заказа:\n" . $order_details;
        $stmt = $pdo->prepare("UPDATE orders SET comment = ? WHERE id = ?");
        $stmt->execute([$updated_comment, $order_id]);
        
        // Фиксируем транзакцию
        $pdo->commit();
        
        // Подготавливаем данные для ответа
        $order_data = [
            'number' => $order_id,
            'total' => number_format($total_amount, 2, '.', ''),
            'delivery_time' => $time,
            'address' => $address,
            'items_count' => count($cart_items)
        ];
        
        sendJsonResponse(true, 'Заказ успешно сохранен в базе данных! Номер вашего заказа: ' . $order_id, [
            'order' => $order_data
        ]);
        
    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $pdo->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Order Handler PDO Error: " . $e->getMessage());
    sendJsonResponse(false, 'Ошибка базы данных: ' . (DEBUG_MODE ? $e->getMessage() : 'Пожалуйста, попробуйте позже.'));
    
} catch (Exception $e) {
    error_log("Order Handler General Error: " . $e->getMessage());
    sendJsonResponse(false, 'Произошла ошибка при обработке заказа: ' . (DEBUG_MODE ? $e->getMessage() : 'Пожалуйста, попробуйте еще раз.'));
}
?>
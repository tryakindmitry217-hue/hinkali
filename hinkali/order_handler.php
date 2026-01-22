<?php
// order_handler.php - Обработчик заказов
header('Content-Type: application/json');

// Подключаем конфигурацию
require_once 'config.php';

// Функция для отправки JSON ответа
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
    
    // Если user_id не передан, пытаемся получить из currentUser
    if (!$user_id && isset($_POST['currentUser'])) {
        $currentUser = json_decode($_POST['currentUser'], true);
        $user_id = $currentUser['id'] ?? 0;
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
    $phone = preg_replace('/[^0-9]/', '', $phone);
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
        $stmt = $pdo->prepare(
            "INSERT INTO orders (user_id, customer_name, phone, address, delivery_time, comment, total_amount, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
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
        
        $order_id = $pdo->lastInsertId();
        
        // Сохраняем детали заказа (в реальном проекте это была бы отдельная таблица order_items)
        // Для простоты сохраним как JSON в поле comment
        $order_details = json_encode([
            'items' => $items_details,
            'delivery_fee' => $delivery_fee,
            'subtotal' => $total_amount - $delivery_fee,
            'total' => $total_amount
        ], JSON_UNESCAPED_UNICODE);
        
        // Обновляем комментарий с деталями заказа
        $stmt = $pdo->prepare(
            "UPDATE orders SET comment = CONCAT(COALESCE(comment, ''), '\n\nДетали заказа:\n', ?) WHERE id = ?"
        );
        $stmt->execute([$order_details, $order_id]);
        
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
    // Ошибка базы данных
    error_log("Database Error: " . $e->getMessage());
    sendJsonResponse(false, 'Ошибка базы данных. Пожалуйста, попробуйте позже.');
    
} catch (Exception $e) {
    // Общая ошибка
    error_log("Order Handler Error: " . $e->getMessage());
    sendJsonResponse(false, 'Произошла ошибка при обработке заказа. Пожалуйста, попробуйте еще раз.');
}
?>
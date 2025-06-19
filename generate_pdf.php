<?php
require_once 'config/db.php';
require_once 'config/pdf.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    
    switch ($type) {
        case 'orders':
            if (!isset($_SESSION['user'])) {
                die('Доступ запрещен');
            }
            
            $user_id = $_POST['user_id'];
            if ($user_id != $_SESSION['user']['id']) {
                die('Доступ запрещен');
            }
            
            // Получаем данные пользователя
            $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $userStmt->execute([$user_id]);
            $user = $userStmt->fetch();
            
            // Получаем заказы пользователя с информацией о статусе
            $ordersStmt = $pdo->prepare("
                SELECT o.*, os.name as status_name, os.color as status_color 
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC
            ");
            $ordersStmt->execute([$user_id]);
            $orders = $ordersStmt->fetchAll();
            
            // Создаем PDF
            $pdf = initPDF('Мои заказы в TechnoCore');
            
            // Информация о пользователе
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Информация о пользователе', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            $pdf->Cell(0, 7, 'Имя: ' . htmlspecialchars($user['name']), 0, 1);
            $pdf->Cell(0, 7, 'Email: ' . htmlspecialchars($user['email']), 0, 1);
            $pdf->Cell(0, 7, 'Телефон: ' . htmlspecialchars($user['phone']), 0, 1);
            $pdf->Ln(10);
            
            // Информация о заказах
            if (count($orders) > 0) {
                $pdf->SetFont('dejavusans', 'B', 14);
                $pdf->Cell(0, 10, 'Мои заказы', 0, 1);
                
                foreach ($orders as $order) {
                    $pdf->SetFont('dejavusans', 'B', 12);
                    $pdf->Cell(0, 7, 'Заказ #' . $order['id'], 0, 1);
                    
                    $pdf->SetFont('dejavusans', '', 10);
                    $pdf->Cell(0, 6, 'Дата: ' . date('d.m.Y H:i', strtotime($order['created_at'])), 0, 1);
                    $pdf->Cell(0, 6, 'Статус: ' . $order['status_name'], 0, 1);
                    $pdf->Cell(0, 6, 'Сумма: ' . number_format($order['total_amount'], 2) . ' ₽', 0, 1);
                    
                    // Получаем товары в заказе
                    $itemsStmt = $pdo->prepare("
                        SELECT * FROM order_items 
                        WHERE order_id = ?
                    ");
                    $itemsStmt->execute([$order['id']]);
                    $items = $itemsStmt->fetchAll();
                    
                    if (count($items) > 0) {
                        $pdf->Ln(3);
                        $pdf->SetFont('dejavusans', 'B', 10);
                        $pdf->Cell(0, 6, 'Состав заказа:', 0, 1);
                        
                        foreach ($items as $item) {
                            $pdf->SetFont('dejavusans', '', 9);
                            $pdf->Cell(0, 6, 
                                $item['product_name'] . ' x ' . $item['quantity'] . ' - ' . 
                                number_format($item['product_price'] * $item['quantity'], 2) . ' ₽', 
                                0, 1);
                        }
                    }
                    
                    $pdf->Ln(5);
                    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+180, $pdf->GetY());
                    $pdf->Ln(5);
                }
            } else {
                $pdf->Cell(0, 10, 'У вас пока нет заказов', 0, 1);
            }
            
            $pdf->Output('my_orders.pdf', 'I');
            break;
            
        case 'product':
            $product_id = $_POST['product_id'] ?? 0;
            
            // Получаем информацию о товаре
            $productStmt = $pdo->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?
            ");
            $productStmt->execute([$product_id]);
            $product = $productStmt->fetch();
            
            if (!$product) {
                die('Товар не найден');
            }
            
            // Создаем PDF
            $pdf = initPDF('Технические характеристики ' . $product['name']);
            
            // Заголовок
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->Cell(0, 10, $product['name'], 0, 1, 'C');
            $pdf->Ln(5);
            
            // Основная информация
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Основная информация', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            
            $pdf->Cell(0, 7, 'Категория: ' . $product['category_name'], 0, 1);
            $pdf->Cell(0, 7, 'Цена: ' . number_format($product['price'], 2) . ' ₽', 0, 1);
            if ($product['sale_price'] > 0) {
                $discount = round(($product['price'] - $product['sale_price']) / $product['price'] * 100);
                $pdf->Cell(0, 7, 'Цена со скидкой: ' . number_format($product['sale_price'], 2) . ' ₽ (-' . $discount . '%)', 0, 1);
            }
            $pdf->Cell(0, 7, 'Гарантия: ' . $product['warranty'], 0, 1);
            $pdf->Ln(5);
            
            // Описание
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Описание', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            $pdf->MultiCell(0, 7, $product['description_short'], 0, 1);
            $pdf->Ln(5);
            
            // Характеристики
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Технические характеристики', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            
            $specs = [
                'Процессор' => $product['cpu'],
                'Видеокарта' => $product['gpu'],
                'Оперативная память' => $product['ram'],
                'Накопитель' => $product['storage'],
                'Материнская плата' => $product['motherboard'],
                'Блок питания' => $product['psu'],
                'Охлаждение' => $product['cooling']
            ];
            
            foreach ($specs as $name => $value) {
                if (!empty($value)) {
                    $pdf->Cell(90, 7, $name, 0, 0);
                    $pdf->Cell(0, 7, $value, 0, 1);
                }
            }
            
            $pdf->Output('product_specs.pdf', 'I');
            break;
            
        case 'user_data':
            if (!isset($_SESSION['user'])) {
                die('Доступ запрещен');
            }
            
            $user_id = $_POST['user_id'];
            if ($user_id != $_SESSION['user']['id']) {
                die('Доступ запрещен');
            }
            
            // Получаем данные пользователя
            $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $userStmt->execute([$user_id]);
            $user = $userStmt->fetch();
            
            // Создаем PDF
            $pdf = initPDF('Мои данные в TechnoCore');
            
            // Заголовок
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->Cell(0, 10, 'Мои личные данные', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Информация о пользователе
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Основная информация', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            
            $pdf->Cell(0, 7, 'Имя: ' . htmlspecialchars($user['name']), 0, 1);
            $pdf->Cell(0, 7, 'Email: ' . htmlspecialchars($user['email']), 0, 1);
            $pdf->Cell(0, 7, 'Телефон: ' . htmlspecialchars($user['phone']), 0, 1);
            $pdf->Cell(0, 7, 'Адрес: ' . htmlspecialchars($user['address']), 0, 1);
            $pdf->Ln(10);
            
            // Дата создания аккаунта
            $pdf->SetFont('dejavusans', 'B', 14);
            $pdf->Cell(0, 10, 'Дата регистрации', 0, 1);
            $pdf->SetFont('dejavusans', '', 12);
            $pdf->Cell(0, 7, date('d.m.Y H:i', strtotime($user['created_at'])), 0, 1);
            
            $pdf->Output('my_data.pdf', 'I');
            break;
            
        default:
            die('Неизвестный тип документа');
    }
} else {
    header("Location: index.php");
}
?>
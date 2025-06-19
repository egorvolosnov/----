<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Редактирование заказа";
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Неверный ID заказа";
    header("Location: orders.php");
    exit;
}

$orderId = (int)$_GET['id'];

// Получаем информацию о заказе
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = "Заказ не найден";
    header("Location: orders.php");
    exit;
}

// Получаем товары в заказе
$stmt = $pdo->prepare("
    SELECT oi.*, p.image_url as product_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();

// Получаем пользователей, статусы и все товары
$users = $pdo->query("SELECT id, name, email FROM users ORDER BY name")->fetchAll();
$statuses = $pdo->query("SELECT * FROM order_statuses")->fetchAll();
$allProducts = $pdo->query("SELECT id, name, price FROM products WHERE stock_quantity > 0 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Обновляем заказ
        $stmt = $pdo->prepare("
            UPDATE orders SET 
                user_id = ?, status_id = ?, customer_name = ?, customer_email = ?,
                customer_phone = ?, customer_address = ?, notes = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['user_id'] ?: null,
            $_POST['status_id'],
            $_POST['customer_name'],
            $_POST['customer_email'],
            $_POST['customer_phone'],
            $_POST['customer_address'],
            $_POST['notes'],
            $orderId
        ]);
        
        // Удаляем старые товары
        $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
        
        // Добавляем новые товары
        $totalAmount = 0;
        if (!empty($_POST['products'])) {
            foreach ($_POST['products'] as $productId => $quantity) {
                if ($quantity > 0) {
                    $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                    $stmt->execute([$productId]);
                    $product = $stmt->fetch();
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $orderId,
                        $productId,
                        $product['name'],
                        $product['price'],
                        $quantity
                    ]);
                    
                    $totalAmount += $product['price'] * $quantity;
                }
            }
            
            // Обновляем общую сумму
            $pdo->prepare("UPDATE orders SET total_amount = ? WHERE id = ?")->execute([$totalAmount, $orderId]);
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Заказ успешно обновлен";
        header("Location: orders.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка при обновлении заказа: " . $e->getMessage();
    }
}
?>

<!-- Остальная часть формы остается без изменений -->

<div class="admin-content">
    <h2>Редактирование заказа #<?= $orderId ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="post" class="order-form">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Информация о заказе</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Пользователь</label>
                            <select id="user_id" name="user_id" class="form-select">
                                <option value="">Гость (без аккаунта)</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= $order['user_id'] == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status_id" class="form-label">Статус</label>
                            <select id="status_id" name="status_id" class="form-select" required>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['id'] ?>" <?= $order['status_id'] == $status['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Информация о клиенте</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Имя</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" 
                                   value="<?= htmlspecialchars($order['customer_name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" id="customer_email" name="customer_email" class="form-control" 
                                   value="<?= htmlspecialchars($order['customer_email']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Телефон</label>
                            <input type="tel" id="customer_phone" name="customer_phone" class="form-control" 
                                   value="<?= htmlspecialchars($order['customer_phone']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Адрес</label>
                            <textarea id="customer_address" name="customer_address" class="form-control" rows="3" required>
                                <?= htmlspecialchars($order['customer_address']) ?>
                            </textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Примечания</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3">
                                <?= htmlspecialchars($order['notes']) ?>
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Товары в заказе</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Товар</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Создаем массив с количеством товаров в заказе
                                    $orderProducts = [];
                                    foreach ($orderItems as $item) {
                                        $orderProducts[$item['product_id']] = $item['quantity'];
                                    }
                                    
                                    foreach ($allProducts as $product): 
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= number_format($product['price'], 0, ',', ' ') ?> ₽</td>
                                        <td>
                                            <input type="number" name="products[<?= $product['id'] ?>]" min="0" 
                                                   value="<?= $orderProducts[$product['id']] ?? 0 ?>" 
                                                   class="form-control quantity-input" style="width: 80px;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="orders.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
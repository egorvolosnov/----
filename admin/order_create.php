<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Создание заказа";
include 'includes/header.php';

// Получаем пользователей и статусы
$users = $pdo->query("SELECT id, name, email FROM users ORDER BY name")->fetchAll();
$statuses = $pdo->query("SELECT * FROM order_statuses")->fetchAll();
$products = $pdo->query("SELECT id, name, price FROM products WHERE stock_quantity > 0 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Создаем заказ
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id, status_id, total_amount, customer_name, customer_email, 
                customer_phone, customer_address, notes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $totalAmount = 0;
        $orderData = [
            $_POST['user_id'] ?: null,
            $_POST['status_id'],
            $totalAmount,
            $_POST['customer_name'],
            $_POST['customer_email'],
            $_POST['customer_phone'],
            $_POST['customer_address'],
            $_POST['notes']
        ];
        
        $stmt->execute($orderData);
        $orderId = $pdo->lastInsertId();
        
        // Добавляем товары
        if (!empty($_POST['products'])) {
            foreach ($_POST['products'] as $productId => $quantity) {
                if ($quantity > 0) {
                    $product = $pdo->prepare("SELECT name, price FROM products WHERE id = ?")->execute([$productId])->fetch();
                    
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
        $_SESSION['success'] = "Заказ успешно создан";
        header("Location: orders.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка при создании заказа: " . $e->getMessage();
    }
}
?>

<div class="admin-content">
    <h2>Создание нового заказа</h2>
    
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
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status_id" class="form-label">Статус</label>
                            <select id="status_id" name="status_id" class="form-select" required>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['id'] ?>"><?= htmlspecialchars($status['name']) ?></option>
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
                            <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" id="customer_email" name="customer_email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Телефон</label>
                            <input type="tel" id="customer_phone" name="customer_phone" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Адрес</label>
                            <textarea id="customer_address" name="customer_address" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Примечания</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
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
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= number_format($product['price'], 0, ',', ' ') ?> ₽</td>
                                        <td>
                                            <input type="number" name="products[<?= $product['id'] ?>]" min="0" 
                                                   value="0" class="form-control quantity-input" style="width: 80px;">
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
            <button type="submit" class="btn btn-primary">Создать заказ</button>
            <a href="orders.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
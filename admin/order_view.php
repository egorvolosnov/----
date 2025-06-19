<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Просмотр заказа";
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

// Получаем информацию о заказе
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email as user_email, os.name as status_name, os.color as status_color
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN order_statuses os ON o.status_id = os.id
    WHERE o.id = ?
");
$stmt->execute([$_GET['id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Получаем товары в заказе
$items = $pdo->prepare("
    SELECT oi.*, p.image_url as product_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items->execute([$_GET['id']]);
$orderItems = $items->fetchAll();

// Получаем все статусы для выпадающего списка
$statuses = $pdo->query("SELECT * FROM order_statuses")->fetchAll();
?>

<div class="container">
    <h2>Заказ #<?= $order['id'] ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Информация о заказе</h4>
                </div>
                <div class="card-body">
                    <p><strong>Статус:</strong> 
                        <span class="badge" style="background-color: <?= $order['status_color'] ?>">
                            <?= htmlspecialchars($order['status_name']) ?>
                        </span>
                    </p>
                    <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Последнее обновление:</strong> <?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></p>
                    <p><strong>Общая сумма:</strong> <?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4>Информация о клиенте</h4>
                </div>
                <div class="card-body">
                    <?php if ($order['user_id']): ?>
                        <p><strong>Пользователь:</strong> 
                            <a href="user_view.php?id=<?= $order['user_id'] ?>">
                                <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['user_email']) ?>)
                            </a>
                        </p>
                    <?php endif; ?>
                    <p><strong>Имя:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                    <p><strong>Адрес:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Товары в заказе</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($orderItems)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Товар</th>
                                        <th>Цена</th>
                                        <th>Кол-во</th>
                                        <th>Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <?php if ($item['product_image']): ?>
                                                    <img src="../../<?= htmlspecialchars($item['product_image']) ?>" width="50" class="mr-2">
                                                <?php endif; ?>
                                                <?= htmlspecialchars($item['product_name']) ?>
                                            </td>
                                            <td><?= number_format($item['product_price'], 0, ',', ' ') ?> ₽</td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= number_format($item['product_price'] * $item['quantity'], 0, ',', ' ') ?> ₽</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">Итого:</td>
                                        <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Нет товаров в заказе</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4>Примечания</h4>
                </div>
                <div class="card-body">
                    <?= $order['notes'] ? nl2br(htmlspecialchars($order['notes'])) : 'Нет примечаний' ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="order_edit.php?id=<?= $order['id'] ?>" class="btn btn-primary">Редактировать</a>
        <a href="orders.php" class="btn btn-secondary">Назад к списку</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
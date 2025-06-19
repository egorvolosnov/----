<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Главная";
include 'includes/header.php';
require_once '../config/db.php';

// Статистика
$productsCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Последние заказы
$latestOrders = $pdo->query("
    SELECT o.id, o.total_amount, o.created_at, os.name as status_name, os.color as status_color
    FROM orders o
    LEFT JOIN order_statuses os ON o.status_id = os.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Товары</h5>
                <p class="card-text display-4"><?= $productsCount ?></p>
                <a href="products.php" class="btn btn-primary">Управление</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Заказы</h5>
                <p class="card-text display-4"><?= $ordersCount ?></p>
                <a href="orders.php" class="btn btn-primary">Управление</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Пользователи</h5>
                <p class="card-text display-4"><?= $usersCount ?></p>
                <a href="users.php" class="btn btn-primary">Управление</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Последние заказы</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($latestOrders)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Сумма</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                                <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?= $order['status_color'] ?>">
                                        <?= htmlspecialchars($order['status_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_view.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Нет заказов</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Просмотр пользователя";
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Неверный ID пользователя";
    header("Location: users.php");
    exit;
}

$userId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Пользователь не найден";
    header("Location: users.php");
    exit;
}

// Получаем заказы пользователя
$stmt = $pdo->prepare("
    SELECT o.id, o.total_amount, o.created_at, os.name as status_name, os.color as status_color
    FROM orders o
    LEFT JOIN order_statuses os ON o.status_id = os.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<!-- Остальная часть шаблона остается без изменений -->

<div class="admin-content">
    <h2>Пользователь: <?= htmlspecialchars($user['name']) ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Основная информация</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Имя</label>
                        <p><?= htmlspecialchars($user['name']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <p><?= htmlspecialchars($user['phone']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Адрес</label>
                        <p><?= nl2br(htmlspecialchars($user['address'])) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Дата регистрации</label>
                        <p><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Заказы пользователя</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
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
                                    <?php foreach ($orders as $order): ?>
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
        </div>
    </div>
    
    <div class="mt-4">
        <a href="user_edit.php?id=<?= $userId ?>" class="btn btn-primary">Редактировать</a>
        <a href="users.php" class="btn btn-secondary">Назад к списку</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
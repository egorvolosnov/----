<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Управление заказами";
include 'includes/header.php';
require_once '../config/db.php';

// Получение списка заказов
$stmt = $pdo->query("
    SELECT o.*, u.name as user_name, os.name as status_name, os.color as status_color
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN order_statuses os ON o.status_id = os.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();

// Получение статусов для фильтра
$statuses = $pdo->query("SELECT * FROM order_statuses")->fetchAll();
?>

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Управление заказами</h2>
        <a href="order_create.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Создать заказ
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Все статусы</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" <?= isset($_GET['status']) && $_GET['status'] == $status['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Дата от</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Фильтровать</button>
                    <a href="orders.php" class="btn btn-secondary ms-2">Сбросить</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td>
                                <?= $order['user_name'] ? htmlspecialchars($order['user_name']) : 'Гость' ?>
                            </td>
                            <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                            <td>
                                <span class="badge" style="background-color: <?= $order['status_color'] ?>">
                                    <?= htmlspecialchars($order['status_name']) ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="order_view.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info" title="Просмотр">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="order_edit.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-secondary" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="order_delete.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" title="Удалить" onclick="return confirm('Вы уверены?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
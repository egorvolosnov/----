<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Управление пользователями";
include 'includes/header.php';
require_once '../config/db.php';

// Получение списка пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<h2>Управление пользователями</h2>
<a href="user_edit.php" class="btn btn-primary" style="margin-bottom: 20px;">
    <i class="fas fa-plus"></i> Добавить пользователя
</a>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
            <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
            <td>
                <a href="user_edit.php?id=<?= $user['id'] ?>" class="btn btn-secondary" style="padding: 5px 10px;">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="user_view.php?id=<?= $user['id'] ?>" class="btn btn-info" style="padding: 5px 10px;">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="user_delete.php?id=<?= $user['id'] ?>" class="btn btn-danger" style="padding: 5px 10px;" 
                   onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
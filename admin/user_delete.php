<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Неверный ID пользователя";
    header("Location: users.php");
    exit;
}

$userId = (int)$_GET['id'];

try {
    // Проверяем, есть ли заказы у пользователя
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    $orderCount = $stmt->fetchColumn();
    
    if ($orderCount > 0) {
        $_SESSION['error'] = "Нельзя удалить пользователя с заказами";
        header("Location: users.php");
        exit;
    }
    
    // Удаляем пользователя
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Пользователь успешно удален";
    } else {
        $_SESSION['error'] = "Пользователь не найден";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Ошибка при удалении пользователя: " . $e->getMessage();
}

header("Location: users.php");
exit;
?>
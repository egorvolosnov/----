<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$orderId = $_GET['id'];

try {
    $pdo->beginTransaction();
    
    // Удаляем товары из заказа
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);
    
    // Удаляем сам заказ
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
    
    $pdo->commit();
    $_SESSION['success'] = "Заказ успешно удален";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Ошибка при удалении заказа: " . $e->getMessage();
}

header("Location: orders.php");
exit;
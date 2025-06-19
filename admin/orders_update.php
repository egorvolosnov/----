<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $statusId = $_POST['status_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status_id = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$statusId, $orderId]);
        
        $_SESSION['success'] = "Статус заказа успешно обновлен";
    } catch (Exception $e) {
        $_SESSION['error'] = "Ошибка при обновлении статуса заказа: " . $e->getMessage();
    }
    
    header("Location: order_view.php?id=" . $orderId);
    exit;
} else {
    header("Location: orders.php");
    exit;
}
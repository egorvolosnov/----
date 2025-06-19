<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Включим отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Неверный ID товара";
    header("Location: products.php");
    exit;
}

$productId = (int)$_GET['id'];

try {
    $pdo->beginTransaction();

    // 1. Получаем информацию о изображениях
    $stmt = $pdo->prepare("SELECT id, image_url FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    $images = $stmt->fetchAll();

    // 2. Удаляем физические файлы изображений
    foreach ($images as $image) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $image['image_url'];
        if (file_exists($filePath) && is_writable($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("Не удалось удалить файл: " . $filePath);
            }
        }
    }

    // 3. Удаляем записи о изображениях из БД
    $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);

    // 4. Удаляем сам товар
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Товар не найден");
    }

    $pdo->commit();
    $_SESSION['success'] = "Товар успешно удалён";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
}

header("Location: products.php");
exit;
?>
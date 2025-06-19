<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Просмотр товара";
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = $_GET['id'];

// product_view.php - исправленные запросы
$product = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$product->execute([$productId]);
$product = $product->fetch();

$images = $pdo->prepare("
    SELECT * FROM product_images 
    WHERE product_id = ?
    ORDER BY is_main DESC, sort_order ASC
");
$images->execute([$productId]);
$images = $images->fetchAll();
?>

<div class="admin-content">
    <h2>Просмотр товара: <?= htmlspecialchars($product['name']) ?></h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Основная информация</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Название</label>
                        <p><?= htmlspecialchars($product['name']) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Категория</label>
                        <p><?= htmlspecialchars($product['category_name'] ?? 'Без категории') ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Цена</label>
                        <p><?= number_format($product['price'], 0, ',', ' ') ?> ₽</p>
                    </div>
                    
                    <?php if ($product['sale_price']): ?>
                    <div class="mb-3">
                        <label class="form-label">Акционная цена</label>
                        <p><?= number_format($product['sale_price'], 0, ',', ' ') ?> ₽</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Количество на складе</label>
                        <p><?= $product['stock_quantity'] ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Краткое описание</label>
                        <p><?= nl2br(htmlspecialchars($product['description_short'])) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Полное описание</label>
                        <p><?= nl2br(htmlspecialchars($product['description_full'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Характеристики</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Процессор</label>
                            <p><?= htmlspecialchars($product['cpu']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Видеокарта</label>
                            <p><?= htmlspecialchars($product['gpu']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Оперативная память</label>
                            <p><?= htmlspecialchars($product['ram']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Накопитель</label>
                            <p><?= htmlspecialchars($product['storage']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Материнская плата</label>
                            <p><?= htmlspecialchars($product['motherboard']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Блок питания</label>
                            <p><?= htmlspecialchars($product['psu']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Охлаждение</label>
                            <p><?= htmlspecialchars($product['cooling']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Гарантия</label>
                            <p><?= htmlspecialchars($product['warranty']) ?> мес.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4>Изображения</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($images as $image): ?>
                        <div class="col-md-4 mb-3">
                            <img src="../<?= htmlspecialchars($image['image_url']) ?>" class="img-fluid">
                            <?php if ($image['is_main']): ?>
                                <span class="badge bg-primary mt-2">Основное</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="product_edit.php?id=<?= $productId ?>" class="btn btn-primary">Редактировать</a>
        <a href="products.php" class="btn btn-secondary">Назад к списку</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
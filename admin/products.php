<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Управление товарами";
include 'includes/header.php';
require_once '../config/db.php';

// Включим отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Получаем параметры фильтрации
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : null;

// Формируем SQL запрос с учетом фильтров
$sql = "
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE 1=1
";

$params = [];

if ($categoryFilter) {
    $sql .= " AND p.category_id = ?";
    $params[] = $categoryFilter;
}

if ($searchQuery) {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $searchQuery . '%';
}

$sql .= " ORDER BY p.created_at DESC";

// Подготавливаем и выполняем запрос
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Получение категорий для фильтра
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<div class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Управление товарами</h2>
        <a href="product_create.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Добавить товар
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
                <div class="col-md-4">
                    <label for="category" class="form-label">Категория</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">Все категории</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Название товара" value="<?= $_GET['search'] ?? '' ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Поиск</button>
                    <a href="products.php" class="btn btn-secondary ms-2">Сбросить</a>
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
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>Наличие</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Товары не найдены</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <?php 
                                    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
                                    $stmt->execute([$product['id']]);
                                    $mainImage = $stmt->fetch();
                                    if ($mainImage && file_exists('../' . $mainImage['image_url'])): ?>
                                        <img src="../../<?= htmlspecialchars($mainImage['image_url']) ?>" width="50" height="50" style="object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">Нет изображения</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'Без категории') ?></td>
                                <td>
                                    <?= number_format($product['price'], 0, ',', ' ') ?> ₽
                                    <?php if ($product['sale_price']): ?>
                                        <br><span class="text-success"><?= number_format($product['sale_price'], 0, ',', ' ') ?> ₽</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $product['stock_quantity'] > 0 ? 
                                        '<span class="text-success">В наличии</span>' : 
                                        '<span class="text-danger">Нет в наличии</span>' ?>
                                </td>
                                <td>
                                    <a href="product_view.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-info" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="product_edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-secondary" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="product_delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" title="Удалить" onclick="return confirm('Вы уверены?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
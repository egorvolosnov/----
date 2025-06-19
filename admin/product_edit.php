<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Редактирование товара";
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = $_GET['id'];

// Получаем информацию о товаре
$product = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$product->execute([$productId]);
$product = $product->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Получаем изображения товара
$images = $pdo->prepare("
    SELECT * FROM product_images 
    WHERE product_id = ?
    ORDER BY is_main DESC, sort_order ASC
");
$stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE id = ?");
$stmt->execute([$imageId]);
$image = $stmt->fetch();
$images = $images->fetchAll();

// Получаем категории
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'category_id' => $_POST['category_id'],
        'description_short' => $_POST['description_short'],
        'description_full' => $_POST['description_full'],
        'price' => $_POST['price'],
        'sale_price' => $_POST['sale_price'] ?: null,
        'stock_quantity' => $_POST['stock_quantity'],
        'cpu' => $_POST['cpu'],
        'gpu' => $_POST['gpu'],
        'ram' => $_POST['ram'],
        'storage' => $_POST['storage'],
        'motherboard' => $_POST['motherboard'],
        'psu' => $_POST['psu'],
        'cooling' => $_POST['cooling'],
        'warranty' => $_POST['warranty'],
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'id' => $productId
    ];

    try {
        $pdo->beginTransaction();

        // Обновляем товар
        $stmt = $pdo->prepare("
            UPDATE products SET 
                name = ?, category_id = ?, description_short = ?, description_full = ?, 
                price = ?, sale_price = ?, stock_quantity = ?, cpu = ?, gpu = ?, 
                ram = ?, storage = ?, motherboard = ?, psu = ?, cooling = ?, 
                warranty = ?, is_featured = ?
            WHERE id = ?
        ");
        $stmt->execute(array_values($data));

        // Обработка загрузки новых изображений
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = '../uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $isMain = $key === 0 && empty($images) ? 1 : 0;
                        $stmt = $pdo->prepare("
                            INSERT INTO product_images (product_id, image_url, is_main) 
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$productId, 'uploads/products/' . $fileName, $isMain]);
                    }
                }
            }
        }

        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imageId) {
                $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE id = ?");
                $stmt->execute([$imageId]);
                $image = $stmt->fetch();

                if ($image) {
                    $filePath = '../' . $image['image_url'];
                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            throw new Exception("Не удалось удалить файл изображения");
                        }
                    }
                    $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imageId]);
                }
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Товар успешно обновлен";
        header("Location: product_view.php?id=" . $productId);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка при обновлении товара: " . $e->getMessage();
    }
}
?>

<div class="admin-content">
    <h2>Редактирование товара: <?= htmlspecialchars($product['name']) ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="product-form">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Основная информация</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название товара</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description_short" class="form-label">Краткое описание</label>
                            <textarea id="description_short" name="description_short" class="form-control" rows="3">
                                <?= htmlspecialchars($product['description_short']) ?>
                            </textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description_full" class="form-label">Полное описание</label>
                            <textarea id="description_full" name="description_full" class="form-control" rows="5">
                                <?= htmlspecialchars($product['description_full']) ?>
                            </textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Цены и наличие</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Цена (руб)</label>
                                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0"
                                    value="<?= $product['price'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Акционная цена (руб)</label>
                                <input type="number" id="sale_price" name="sale_price" class="form-control" step="0.01"
                                    min="0" value="<?= $product['sale_price'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">Количество на складе</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0"
                                value="<?= $product['stock_quantity'] ?>" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                <?= $product['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Рекомендуемый товар</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Изображения товара</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Текущие изображения</label>
                            <div class="row">
                                <?php foreach ($images as $image): ?>
                                    <div class="col-md-4 mb-3">
                                        <img src="../<?= htmlspecialchars($image['image_url']) ?>" class="img-fluid mb-2">
                                        <?php if ($image['is_main']): ?>
                                            <span class="badge bg-primary">Основное</span>
                                        <?php endif; ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_images[]"
                                                value="<?= $image['id'] ?>" id="delete_image_<?= $image['id'] ?>">
                                            <label class="form-check-label" for="delete_image_<?= $image['id'] ?>">
                                                Удалить
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Добавить новые изображения (максимум 3)</label>
                            <input type="file" id="images" name="images[]" class="form-control" multiple
                                accept="image/*">
                            <small class="text-muted">Первое изображение будет основным, если нет текущих</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Характеристики</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpu" class="form-label">Процессор</label>
                                <input type="text" id="cpu" name="cpu" class="form-control"
                                    value="<?= htmlspecialchars($product['cpu']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gpu" class="form-label">Видеокарта</label>
                                <input type="text" id="gpu" name="gpu" class="form-control"
                                    value="<?= htmlspecialchars($product['gpu']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ram" class="form-label">Оперативная память</label>
                                <input type="text" id="ram" name="ram" class="form-control"
                                    value="<?= htmlspecialchars($product['ram']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="storage" class="form-label">Накопитель</label>
                                <input type="text" id="storage" name="storage" class="form-control"
                                    value="<?= htmlspecialchars($product['storage']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="motherboard" class="form-label">Материнская плата</label>
                                <input type="text" id="motherboard" name="motherboard" class="form-control"
                                    value="<?= htmlspecialchars($product['motherboard']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="psu" class="form-label">Блок питания</label>
                                <input type="text" id="psu" name="psu" class="form-control"
                                    value="<?= htmlspecialchars($product['psu']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cooling" class="form-label">Охлаждение</label>
                                <input type="text" id="cooling" name="cooling" class="form-control"
                                    value="<?= htmlspecialchars($product['cooling']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="warranty" class="form-label">Гарантия (мес.)</label>
                                <input type="number" id="warranty" name="warranty" class="form-control" min="0"
                                    value="<?= $product['warranty'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="product_view.php?id=<?= $productId ?>" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
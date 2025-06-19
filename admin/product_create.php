<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = "Добавление товара";
include 'includes/header.php';

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
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0
    ];

    try {
        $pdo->beginTransaction();

        // Создаем товар
        $stmt = $pdo->prepare("
            INSERT INTO products (
                name, category_id, description_short, description_full, price, sale_price, 
                stock_quantity, cpu, gpu, ram, storage, motherboard, psu, cooling, warranty, is_featured
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute(array_values($data));
        $productId = $pdo->lastInsertId();

        // Обработка загрузки изображений
        // Обработка загрузки изображений
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = '../uploads/products/';

            // Создаем папку, если ее нет
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $error = "Не удалось создать директорию для загрузки";
                }
            }

            // Проверяем доступность папки для записи
            if (!is_writable($uploadDir)) {
                $error = "Директория для загрузки недоступна для записи";
            }

            if (!isset($error)) {
                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                        $targetPath = $uploadDir . $fileName;

                        // Проверяем тип файла
                        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                        if (!in_array($fileType, $allowedTypes)) {
                            $error = "Допустимы только JPG, JPEG, PNG и GIF файлы";
                            continue;
                        }

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            // Сохраняем путь относительно корня сайта (без ../../)
                            $imagePath = 'uploads/products/' . $fileName;
                            $isMain = $key === 0 ? 1 : 0;

                            $stmt = $pdo->prepare("
                        INSERT INTO product_images (product_id, image_url, is_main) 
                        VALUES (?, ?, ?)
                    ");
                            $stmt->execute([$productId, $imagePath, $isMain]);
                        } else {
                            $error = "Ошибка при загрузке файла " . $_FILES['images']['name'][$key];
                        }
                    }
                }
            }
        }
        $pdo->commit();
        $_SESSION['success'] = "Товар успешно добавлен";
        header("Location: products.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка при добавлении товара: " . $e->getMessage();
    }
}
?>

<div class="admin-content">
    <h2>Добавление нового товара</h2>

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
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description_short" class="form-label">Краткое описание</label>
                            <textarea id="description_short" name="description_short" class="form-control"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description_full" class="form-label">Полное описание</label>
                            <textarea id="description_full" name="description_full" class="form-control"
                                rows="5"></textarea>
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
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Акционная цена (руб)</label>
                                <input type="number" id="sale_price" name="sale_price" class="form-control" step="0.01"
                                    min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">Количество на складе</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0"
                                value="0" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
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
                            <label for="images" class="form-label">Загрузить изображения (максимум 3)</label>
                            <input type="file" id="images" name="images[]" class="form-control" multiple
                                accept="image/*">
                            <small class="text-muted">Первое изображение будет основным</small>
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
            <a href="products.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>z
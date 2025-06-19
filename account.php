<?php
// 1. Старт сессии и проверка авторизации
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// 2. Запросы к БД
try {
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user']['id']]);
    $user = $userStmt->fetch();

    // echo "<pre>Данные пользователя: ";
    // var_dump($user);
    // echo "</pre>";

    $ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $ordersStmt->execute([$_SESSION['user']['id']]);
    $orders = $ordersStmt->fetchAll();

    // echo "<pre>Заказы пользователя: ";
    // var_dump($orders);
    // echo "</pre>";
} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}

// 3. Подключение шаблонов
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Личный кабинет</h1>
            <div class="breadcrumbs">
                <a href="index.php">Главная</a>
                <span>/</span>
                <span>Личный кабинет</span>
            </div>
        </div>
    </section>

    <section class="account">
        <div class="container account-container">
            <div class="account-sidebar">
                <ul class="account-menu">
                    <li><a href="account.php" class="active">Мои заказы</a></li>
                    <li><a href="account-data.php">Личные данные</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                </ul>
            </div>

            <div class="account-content">
                <div class="account-section">
                    <h2>Добро пожаловать, <?= htmlspecialchars($user['name']) ?></h2>

                    <h3>Мои заказы</h3>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <div>Заказ #<?= $order['id'] ?></div>
                                        <div class="order-date"><?= date('d.m.Y', strtotime($order['created_at'])) ?></div>
                                    </div>
                                    <div class="order-status status-<?= $order['status'] ?>">
                                        <?= $order['status'] === 'completed' ? 'Завершен' : 'В обработке' ?>
                                    </div>
                                </div>
                                <div class="order-summary">
                                    <span>Сумма:</span>
                                    <span><?= number_format($order['total'], 2) ?> ₽</span>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top: 20px;">
                            <form method="post" action="generate_pdf.php" target="_blank">
                                <input type="hidden" name="type" value="orders">
                                <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-file-pdf"></i> Выгрузить заказы в PDF
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p>У вас пока нет заказов</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php';
// Добавляем кнопку выгрузки PDF
?>
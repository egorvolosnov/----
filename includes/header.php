<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'TechnoCore' ?></title>
    <link rel="stylesheet" href="src/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container header-container">
        <a href="index.php" class="logo">TechnoCore</a>
        <nav>
            <ul>
                <li><a href="index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'class="active"' : '' ?>>Главная</a></li>
                <li><a href="catalog.php" <?= basename($_SERVER['PHP_SELF']) === 'catalog.php' ? 'class="active"' : '' ?>>Каталог</a></li>
                <li><a href="builder.php" <?= basename($_SERVER['PHP_SELF']) === 'builder.php' ? 'class="active"' : '' ?>>Сборка ПК</a></li>
                <li><a href="cart.php" <?= basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'class="active"' : '' ?>>Корзина</a></li>
                <li><a href="account.php" <?= basename($_SERVER['PHP_SELF']) === 'account.php' ? 'class="active"' : '' ?>>Личный кабинет</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a href="cart.php"><i class="fas fa-shopping-cart cart-icon"></i></a>
            <a href="account.php"><i class="fas fa-user user-icon"></i></a>
        </div>
    </div>
</header>
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Админ-панель | TechnoCore</title>
    <link rel="stylesheet" href="../../src/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    :root {
        --text-color: #333;
        --light-text: #6c757d;
        --bg-color: #f8f9fa;
        --card-bg: #fff;
        --darker-bg: #e9ecef;
        --border-color: #dee2e6;
        --accent-blue: #0d6efd;
        --accent-orange: #fd7e14;
        --border-radius: 8px;
        --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }

    .admin-sidebar {
        background-color: var(--card-bg);
        padding: 20px;
        border-radius: var(--border-radius);
        margin-bottom: 30px;
        box-shadow: var(--box-shadow);
    }

    .admin-menu {
        list-style: none;
        padding: 0;
    }

    .admin-menu li {
        margin-bottom: 8px;
    }

    .admin-menu a {
        display: block;
        padding: 10px 15px;
        border-radius: var(--border-radius);
        transition: all 0.3s;
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
    }

    .admin-menu a:hover, 
    .admin-menu a.active {
        background-color: var(--accent-blue);
        color: white;
    }

    .admin-content {
        background-color: var(--card-bg);
        padding: 30px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        flex: 1;
        margin-bottom: 30px;
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: var(--darker-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
        font-weight: 600;
    }

    .card-body {
        padding: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .data-table th, 
    .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .data-table th {
        background-color: var(--darker-bg);
        color: var(--text-color);
        font-weight: 600;
    }

    .data-table tr:hover td {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 500;
    }

    .form-control, .form-select {
        padding: 10px 15px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }

    .alert {
        border-radius: var(--border-radius);
        padding: 15px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        color: var(--text-color);
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--border-radius);
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 0.875rem;
    }

    .text-muted {
        color: var(--light-text) !important;
    }

    /* Улучшения для форм */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .quantity-input {
        width: 80px !important;
        text-align: center;
    }

    /* Улучшения для изображений */
    .img-fluid {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
    }

    /* Улучшения для заголовков */
    h2, h3, h4, h5, h6 {
        color: var(--text-color);
        margin-bottom: 1.2rem;
    }

    h2 {
        font-size: 1.8rem;
        font-weight: 600;
    }

    /* Улучшения для текста */
    p {
        margin-bottom: 1rem;
    }

    /* Улучшения для навигации */
    .nav-link {
        padding: 0.5rem 1rem;
    }

    /* Улучшения для текстовых областей */
    textarea.form-control {
        min-height: 120px;
    }
</style>
</head>
<body>
<header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php" class="logo text-white text-decoration-none h4 mb-0">
            TechnoCore <small class="text-muted">Admin</small>
        </a>
        <nav>
            <ul class="nav">
                <li class="nav-item">
                    <a href="products.php" class="nav-link text-white <?= $currentPage == 'products.php' ? 'active' : '' ?>">
                        Товары
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders.php" class="nav-link text-white <?= $currentPage == 'orders.php' ? 'active' : '' ?>">
                        Заказы
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link text-white <?= $currentPage == 'users.php' ? 'active' : '' ?>">
                        Пользователи
                    </a>
                </li>
            </ul>
        </nav>
        <div>
            <a href="../logout.php" class="btn btn-outline-light">
                <i class="fas fa-sign-out-alt"></i> Выйти
            </a>
        </div>
    </div>
</header>
<main class="admin-main py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="admin-sidebar">
                    <ul class="admin-menu">
                        <li><a href="index.php" <?= $currentPage == 'index.php' ? 'class="active"' : '' ?>>Главная</a></li>
                        <li><a href="products.php" <?= $currentPage == 'products.php' ? 'class="active"' : '' ?>>Товары</a></li>
                        <li><a href="orders.php" <?= $currentPage == 'orders.php' ? 'class="active"' : '' ?>>Заказы</a></li>
                        <li><a href="users.php" <?= $currentPage == 'users.php' ? 'class="active"' : '' ?>>Пользователи</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <div class="admin-content">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
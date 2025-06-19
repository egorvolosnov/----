<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'egorvoat_db');
define('DB_USER', 'egorvoat_db');
define('DB_PASS', '16kWSL6%pmc3');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Подключение к БД успешно установлено.";
} catch (PDOException $e) {
    error_log("Ошибка подключения к БД: " . $e->getMessage());
    die("Произошла ошибка. Пожалуйста, попробуйте позже.");
}
<?php
session_start();

// Сохраняем имя пользователя перед уничтожением сессии
$username = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : '';

// Очищаем сессию
$_SESSION = array();
session_destroy();

// Перенаправляем с сообщением
header("Location: index.php?logout=1&user=" . urlencode($username));
exit();
?>
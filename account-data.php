<?php
// 1. Старт сессии и проверка авторизации
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Инициализация переменных
$error = '';
$success = '';

// Получаем текущие данные пользователя
try {
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user']['id']]);
    $user = $userStmt->fetch();
} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}

// Обработка формы изменения данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // Проверка текущего пароля (если меняется)
        if (!empty($new_password)) {
            if (!password_verify($current_password, $user['password'])) {
                $error = "Неверный текущий пароль";
            } elseif ($new_password !== $confirm_password) {
                $error = "Новые пароли не совпадают";
            } elseif (strlen($new_password) < 6) {
                $error = "Пароль должен содержать минимум 6 символов";
            }
        }

        // Если нет ошибок - обновляем данные
        if (empty($error)) {
            // Подготовка данных для обновления
            $update_data = [
                'name' => $name,
                'email' => $email,
                'id' => $_SESSION['user']['id']
            ];
            
            $sql = "UPDATE users SET name = :name, email = :email";
            
            // Если меняем пароль
            if (!empty($new_password)) {
                $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $sql .= ", password = :password";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($update_data);
            
            // Обновляем данные в сессии
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            
            $success = "Данные успешно обновлены!";
            
            // Перезагружаем данные пользователя
            $userStmt->execute([$_SESSION['user']['id']]);
            $user = $userStmt->fetch();
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Этот email уже занят другим пользователем";
        } else {
            $error = "Ошибка при обновлении данных: " . $e->getMessage();
        }
    }
}

// Подключение шаблонов
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Личный кабинет</h1>
            <div class="breadcrumbs">
                <a href="index.php">Главная</a>
                <span>/</span>
                <span>Личные данные</span>
            </div>
        </div>
    </section>

    <section class="account">
        <div class="container account-container">
            <div class="account-sidebar">
                <ul class="account-menu">
                    <li><a href="account.php">Мои заказы</a></li>
                    <li><a href="account-data.php" class="active">Личные данные</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                </ul>
            </div>

            <div class="account-content">
                <div class="account-section">
                    <h2>Личные данные</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success-message"><?= $success ?></div>
                    <?php endif; ?>
                    
                    <form method="post" class="account-form">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <h3>Смена пароля</h3>
                        <p class="form-note">Оставьте эти поля пустыми, если не хотите менять пароль</p>
                        
                        <div class="form-group password-toggle">
                            <label for="current_password">Текущий пароль</label>
                            <input type="password" id="current_password" name="current_password">
                            <span class="toggle-password strikethrough">👁️</span>
                        </div>
                        
                        <div class="form-group password-toggle">
                            <label for="new_password">Новый пароль</label>
                            <input type="password" id="new_password" name="new_password">
                            <span class="toggle-password strikethrough">👁️</span>
                        </div>
                        
                        <div class="form-group password-toggle">
                            <label for="confirm_password">Подтвердите новый пароль</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <span class="toggle-password strikethrough">👁️</span>
                        </div>
                        
                        <button type="submit" class="btn-primary-new">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('strikethrough');
            } else {
                input.type = 'password';
                this.classList.add('strikethrough');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
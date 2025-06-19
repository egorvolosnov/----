<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        header("Location: account.php");
    } else {
        $error = "Неверный email или пароль";
    }
}
?>
<link rel="stylesheet" href="src/css/style.css">
<div class="auth-container">
    <h2 class="auth-title">Вход в аккаунт</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">Регистрация прошла успешно! Теперь вы можете войти.</div>
    <?php endif; ?>
    
    <form class="auth-form" method="post">
        <div class="form-group">
            <label for="login-email">Email</label>
            <input type="email" id="login-email" name="email" placeholder="example@mail.com" required>
        </div>
        
        <div class="form-group password-toggle">
            <label for="login-password">Пароль</label>
            <input type="password" id="login-password" name="password" placeholder="Введите ваш пароль" required>
            <span class="toggle-password">👁️</span>
        </div>
        
        <button type="submit" class="auth-submit">Войти</button>
    </form>
    
    <div class="auth-footer">
        Нет аккаунта? <a href="register.php" class="auth-switch">Зарегистрироваться</a>
    </div>
</div>

<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('strikethrough');
                this.innerHTML = '👁️';
            } else {
                input.type = 'password';
                this.classList.add('strikethrough');
                this.innerHTML = '👁️';
            }
        });
        
        // Инициализация
        this.classList.add('strikethrough');
    });
</script>

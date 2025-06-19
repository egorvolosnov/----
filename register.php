<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        
        header("Location: login.php?success=1");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Этот email уже зарегистрирован";
        } else {
            $error = "Ошибка: " . $e->getMessage();
        }
    }
}
?>
<link rel="stylesheet" href="src/css/style.css">
<div class="auth-container">
    <h2 class="auth-title">Создать аккаунт</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <form class="auth-form" method="post">
        <div class="form-group">
            <label for="name">Ваше имя</label>
            <input type="text" id="name" name="name" placeholder="Иван Иванов" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="example@mail.com" required>
        </div>
        
        <div class="form-group password-toggle">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" placeholder="Не менее 6 символов" required>
            <span class="toggle-password">👁️</span>
        </div>
        
        <button type="submit" class="auth-submit">Зарегистрироваться</button>
    </form>
    
    <div class="auth-footer">
        Уже есть аккаунт? <a href="login.php" class="auth-switch">Войти</a>
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
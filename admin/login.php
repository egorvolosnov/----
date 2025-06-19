<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin'] = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'role' => $admin['role']
        ];
        header("Location: index.php");
    } else {
        $error = "Неверные учетные данные";
    }
}
?>

<link rel="stylesheet" href="../src/css/style.css">
<div class="auth-container" style="max-width: 400px; margin: 100px auto;">
    <h2 class="auth-title">Вход в админ-панель</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <form class="auth-form" method="post">
        <div class="form-group">
            <label for="username">Логин</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group password-toggle">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
            <span class="toggle-password">👁️</span>
        </div>
        
        <button type="submit" class="auth-submit">Войти</button>
    </form>
</div>

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
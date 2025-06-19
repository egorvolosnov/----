<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$pageTitle = isset($_GET['id']) ? "Редактирование пользователя" : "Добавление пользователя";
include 'includes/header.php';

$user = null;
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['error'] = "Пользователь не найден";
        header("Location: users.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address']
    ];

    try {
        if (isset($_POST['id'])) {
            // Редактирование существующего пользователя
            $data['id'] = $_POST['id'];
            $stmt = $pdo->prepare("
                UPDATE users SET 
                    name = ?, email = ?, phone = ?, address = ?
                WHERE id = ?
            ");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address'], $data['id']]);
            
            if (!empty($_POST['password'])) {
                $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$passwordHash, $data['id']]);
            }
            
            $_SESSION['success'] = "Пользователь успешно обновлен";
        } else {
            // Создание нового пользователя
            if (empty($_POST['password'])) {
                throw new Exception("Пароль обязателен для нового пользователя");
            }
            
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, phone, address, password)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address'], $passwordHash]);
            $_SESSION['success'] = "Пользователь успешно создан";
        }
        
        header("Location: users.php");
        exit;
    } catch (Exception $e) {
        $error = "Ошибка: " . $e->getMessage();
    }
}
?>

<!-- Остальная часть формы остается без изменений -->

<div class="admin-content">
    <h2><?= isset($user) ? 'Редактирование пользователя' : 'Добавление нового пользователя' ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="post">
        <?php if (isset($user)): ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= isset($user) ? htmlspecialchars($user['name']) : '' ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= isset($user) ? htmlspecialchars($user['email']) : '' ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="tel" id="phone" name="phone" class="form-control" 
                           value="<?= isset($user) ? htmlspecialchars($user['phone']) : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Адрес</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?= 
                        isset($user) ? htmlspecialchars($user['address']) : '' ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           <?= !isset($user) ? 'required' : '' ?>>
                    <?php if (isset($user)): ?>
                        <small class="text-muted">Оставьте пустым, если не хотите менять пароль</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="users.php" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/header.php'; ?>
<main>
    <section class="page-header">
        <div class="container">
            <h1>Корзина</h1>
            <div class="breadcrumbs">
                <a href="index.php">Главная</a>
                <span>/</span>
                <span>Корзина</span>
            </div>
        </div>
    </section>

    <section class="cart">
        <div class="container">
            <div class="cart-items">
                <div class="cart-item">
                    <img src="src/img/4.png" alt="Игровой ПК" class="cart-item-image">
                    <div class="cart-item-details">
                        <h3>ULTRA GAMING PRO RTX 4090</h3>
                        <div class="cart-item-price">249 999 ₽</div>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button class="quantity-btn">-</button>
                                <span>1</span>
                                <button class="quantity-btn">+</button>
                            </div>
                            <button class="remove-btn"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cart-summary">
                <div class="summary-item">
                    <span>Товары (1)</span>
                    <span>249 999 ₽</span>
                </div>
                <div class="summary-item">
                    <span>Доставка</span>
                    <span>Бесплатно</span>
                </div>
                <div class="summary-total">
                    <span>Итого:</span>
                    <span>249 999 ₽</span>
                </div>
                <button class="btn btn-primary" style="width: 100%; margin-top: 20px;">Оформить заказ</button>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
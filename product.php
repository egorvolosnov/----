<?php include 'includes/header.php'; ?>

    <main class="product-page">
        <div class="container">
            <!-- Хлебные крошки -->
            <div class="breadcrumbs" style="margin-bottom: 20px;">
                <a href="index.php">Главная</a> / 
                <a href="catalog.php">Каталог</a> / 
                <a href="catalog.php?category=gaming">Игровые ПК</a> /
                <span>Gaming PC PRO X9000</span>
            </div>

            <!-- Основная информация о товаре -->
            <div class="product-grid">
                <div class="product-gallery">
                    <img src="src/img/4.png" alt="Gaming PC PRO X9000" class="main-image" id="mainImage">
                    <div class="thumbnail-container">
                        <img src="src/img/1.png" alt="ПК вид 1" class="thumbnail active" onclick="changeImage(this, 'src/img/1.png')">
                        <img src="src/img/2.png" alt="ПК вид 2" class="thumbnail" onclick="changeImage(this, 'src/img/2.png')">
                        <img src="src/img/3.png" alt="ПК вид 3" class="thumbnail" onclick="changeImage(this, 'src/img/3.png')">
                    </div>
                </div>

                <div class="product-info">
                    <h1 class="product-title">Gaming PC PRO X9000</h1>
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="review-count">(42 отзыва)</span>
                    </div>

                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-box-open"></i>
                            <span>В наличии: 5 шт.</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-barcode"></i>
                            <span>Артикул: TC-PC9000</span>
                        </div>
                    </div>

                    <div class="product-price">
                        149 999 ₽ <span class="product-old-price">169 999 ₽</span>
                        <span class="product-discount">-12%</span>
                    </div>

                    <div class="product-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> В корзину
                        </button>
                        <button class="btn btn-secondary">
                            Купить в 1 клик
                        </button>
                        <button class="btn wishlist-btn" title="Добавить в избранное">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="product-description">
                        <h3>Описание</h3>
                        <p>Мощный игровой компьютер PRO X9000 с видеокартой NVIDIA GeForce RTX 4090 и процессором Intel Core i9-14900K. Идеальное решение для геймеров и профессионалов. Охлаждение жидкостное, корпус с RGB-подсветкой.</p>
                    </div>
                </div>
            </div>

            <!-- Технические характеристики -->
            <div class="product-specs">
                <h2 class="specs-title">Технические характеристики</h2>
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-name">Процессор</span>
                        <span class="spec-value">Intel Core i9-14900K</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Видеокарта</span>
                        <span class="spec-value">NVIDIA GeForce RTX 4090 (24GB)</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Оперативная память</span>
                        <span class="spec-value">32GB DDR5 5600MHz</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Накопитель</span>
                        <span class="spec-value">2TB NVMe SSD</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Материнская плата</span>
                        <span class="spec-value">ASUS ROG MAXIMUS Z790 HERO</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Блок питания</span>
                        <span class="spec-value">1200W 80+ Platinum</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Охлаждение</span>
                        <span class="spec-value">Жидкостное Corsair iCUE H150i</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-name">Гарантия</span>
                        <span class="spec-value">36 месяцев</span>
                    </div>
                </div>
            </div>

            <!-- Табы с дополнительной информацией -->
            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-btn active" onclick="openTab(event, 'details')">Подробности</button>
                    <button class="tab-btn" onclick="openTab(event, 'reviews')">Отзывы (42)</button>
                    <button class="tab-btn" onclick="openTab(event, 'delivery')">Доставка и оплата</button>
                    <button class="tab-btn" onclick="openTab(event, 'warranty')">Гарантия</button>
                </div>

                <div id="details" class="tab-content active">
                    <h3>Полное описание</h3>
                    <p>Игровой компьютер PRO X9000 - это флагманская сборка от TechnoCore с топовыми компонентами. Корпус Lian Li PC-O11 Dynamic с 9 RGB-вентиляторами обеспечивает отличное охлаждение и эффектный внешний вид. Система способна запускать любые современные игры в 4K с максимальными настройками.</p>
                    <ul style="margin-top: 15px; padding-left: 20px;">
                        <li style="margin-bottom: 8px;">Процессор Intel Core i9-14900K (24 ядра, 5.8 GHz)</li>
                        <li style="margin-bottom: 8px;">Видеокарта NVIDIA GeForce RTX 4090 с подсветкой</li>
                        <li style="margin-bottom: 8px;">Оперативная память Kingston Fury DDR5 5600MHz</li>
                    </ul>
                </div>

                <div id="reviews" class="tab-content">
                    <h3>Отзывы покупателей</h3>
                    <div style="margin-top: 20px; background: var(--darker-bg); padding: 20px; border-radius: var(--border-radius);">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <img src="src/img/m`.png" alt="Алексей" style="border-radius: 50%; width:50px;">
                            <div>
                                <div style="font-weight: 600;">Алексей</div>
                                <div style="color: var(--accent-orange);">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p>"Играю в Cyberpunk 2077 на ультра настройках с рейтрейсингом - все летает! Сборка качественная, все аккуратно уложено. Доставили на день раньше обещанного срока."</p>
                        <div style="margin-top: 10px; font-size: 14px; color: var(--text-secondary);">12.05.2024</div>
                    </div>
                </div>

                <div id="delivery" class="tab-content">
                    <h3>Условия доставки</h3>
                    <p>Доставка по Москве - 500 ₽ (бесплатно при заказе от 50 000 ₽). По России - от 1000 ₽ в зависимости от региона. Самовывоз из нашего магазина бесплатно.</p>
                </div>

                <div id="warranty" class="tab-content">
                    <h3>Гарантийные обязательства</h3>
                    <p>На всю сборку предоставляется гарантия 36 месяцев. Бесплатный выезд мастера в течение гарантийного срока.</p>
                </div>
            </div>

            <!-- Похожие товары -->
            <div class="related-products">
                <h2 class="section-title">Похожие товары</h2>
                <div class="products-grid">
                    <div class="product-card">
                        <img src="src/img/2.png" alt="Игровой ПК" style="width: 100%; border-radius: var(--border-radius);">
                        <div style="padding: 15px;">
                            <h3 style="margin-bottom: 10px;">Gaming PC X7000</h3>
                            <div style="color: var(--accent-orange); font-weight: 600; font-size: 20px;">119 999 ₽</div>
                        </div>
                    </div>
                    <!-- Еще 3 аналогичных товара -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (подключается через PHP) -->
    <?php include 'includes/footer.php'; ?>

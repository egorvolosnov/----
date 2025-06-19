<?php
include 'includes/header.php';
?>
<main>
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Соберите свой идеальный ПК с TechnoCore</h1>
                <p>Более 1000 готовых конфигураций для игр, работы и творчества. Присоединяйтесь к тысячам довольных
                    клиентов!</p>
                <div class="hero-buttons">
                    <a href="catalog.php" class="btn btn-primary">В каталог</a>
                    <a href="builder.php" class="btn btn-secondary">Собрать ПК</a>
                </div>
            </div>
            <img src="src/img/first.png" alt="Игровой ПК" class="hero-image">
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-title">Почему выбирают <span>TechnoCore</span></h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Надежность</h3>
                    <p>Гарантия до 3 лет на все сборки. Каждый ПК проходит строгий 37-пунктный тест перед отправкой.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h3>Поддержка 24/7</h3>
                    <p>Наши специалисты готовы помочь в любое время со средним временем ответа всего 5 минут.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-star"></i></div>
                    <h3>Доверие клиентов</h3>
                    <p>Рейтинг 4.9/5.0 на ведущих платформах. 98% клиентов рекомендуют нас друзьям.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="categories">
        <div class="container">
            <h2 class="section-title">Категории <span>товаров</span></h2>
            <div class="categories-grid">
                <div class="category-card">
                    <img src="/src/img/1.png" alt="Игровые ПК" class="category-image">
                    <div class="category-content">
                        <h3>Игровые ПК</h3>
                        <p>Мощные системы с новейшими видеокартами NVIDIA RTX 40-й серии</p>
                        <a href="catalog.php?category=gaming" class="btn btn-primary">Смотреть</a>
                    </div>
                </div>
                <div class="category-card">
                    <img src="/src/img/4.png" alt="Рабочие станции" class="category-image">
                    <div class="category-content">
                        <h3>Рабочие станции</h3>
                        <p>Производительные ПК для профессионалов с процессорами Intel Core 14-го поколения</p>
                        <a href="catalog.php?category=workstation" class="btn btn-primary">Смотреть</a>
                    </div>
                </div>
                <div class="category-card">
                    <img src="/src/img/3.png" alt="Комплектующие" class="category-image">
                    <div class="category-content">
                        <h3>Комплектующие</h3>
                        <p>Более 5000 наименований для апгрейда или сборки ПК с нуля</p>
                        <a href="catalog.php?category=components" class="btn btn-primary">Смотреть</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Отзывы <span>клиентов</span></h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="src/img/m`.png" alt="Алексей" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-author">Алексей</div>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">"Заказал игровой ПК в TechnoCore и не пожалел! Все работает
                        идеально, сборка аккуратная, доставили быстрее обещанного срока. Теперь могу играть в любые
                        игры на максималках!"</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="src/img/zh.png" alt="Екатерина" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-author">Екатерина</div>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">"Нужна была мощная рабочая станция для 3D-моделирования. Специалисты
                        TechnoCore помогли подобрать оптимальную конфигурацию. Работает безупречно уже больше года!"
                    </p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="src/img/m2.png" alt="Дмитрий" class="testimonial-avatar">
                        <div>
                            <div class="testimonial-author">Дмитрий</div>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">"Собрал ПК через онлайн-конфигуратор - очень удобный инструмент!
                        Получил именно то, что хотел, плюс приятный бонус в виде игровой мыши. Рекомендую!"</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Готовы собрать свой идеальный ПК?</h2>
            <p>Воспользуйтесь нашим онлайн-конфигуратором или выберите одну из готовых сборок. Получите бесплатную
                консультацию от наших специалистов!</p>
            <div style="display: flex; gap: 20px; justify-content: center;">
                <a href="builder.php" class="btn btn-secondary">Собрать ПК</a>
                <a href="catalog.php" class="btn btn-primary">Готовые сборки</a>
            </div>
        </div>
    </section>
    <section class="stores">
        <div class="container">
            <h2 class="section-title">Наши <span>магазины</span></h2>
            <div class="stores-content">
                <div class="stores-info">
                    <div class="store-card">
                        <h3><i class="fas fa-map-marker-alt" style="color: var(--accent-orange);"></i> Москва</h3>
                        <p><i class="fas fa-clock"></i> Пн-Пт: 9:00-21:00, Сб-Вс: 10:00-20:00</p>
                        <p><i class="fas fa-phone"></i> +7 (495) 123-45-67</p>
                        <p><i class="fas fa-map-pin"></i> ул. Техническая, 15, ТЦ "Гаджет"</p>
                    </div>
                    <div class="store-card">
                        <h3><i class="fas fa-map-marker-alt" style="color: var(--accent-orange);"></i> Санкт-Петербург</h3>
                        <p><i class="fas fa-clock"></i> Пн-Пт: 10:00-20:00, Сб-Вс: 10:00-19:00</p>
                        <p><i class="fas fa-phone"></i> +7 (812) 765-43-21</p>
                        <p><i class="fas fa-map-pin"></i> пр. Науки, 42, ТРК "Технополис"</p>
                    </div>
                    <div class="store-card">
                        <h3><i class="fas fa-map-marker-alt" style="color: var(--accent-orange);"></i> Екатеринбург</h3>
                        <p><i class="fas fa-clock"></i> Пн-Пт: 10:00-20:00, Сб-Вс: 10:00-18:00</p>
                        <p><i class="fas fa-phone"></i> +7 (343) 987-65-43</p>
                        <p><i class="fas fa-map-pin"></i> ул. Мамина-Сибиряка, 132, ТЦ "Антей"</p>
                    </div>
                </div>
                <div class="stores-map-container">
                <div id="map" style="width: 100%; height: 100%;"></div>
            </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
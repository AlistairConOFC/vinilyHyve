<main>
    <!-- Слайдер -->
    <section class="hero-slider">
        <div class="container">
            <div class="slider-container" id="mainSlider">
                <?php foreach ($hits as $index => $hit): ?>
                <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                    <div class="slide-content">
                        <div class="slide-info">
                            <span class="slide-badge">🔥 Хит продаж</span>
                            <h2><?= htmlspecialchars($hit['artist']) ?></h2>
                            <p><?= htmlspecialchars($hit['title']) ?></p>
                            <div class="slide-price">от <?= number_format($hit['min_price'], 0, '.', ' ') ?> ₽</div>
                            <a href="/product/<?= $hit['id'] ?>" class="btn btn-primary">Купить</a>
                        </div>
                        <div class="slide-image">
                            <img src="/uploads/covers/<?= htmlspecialchars($hit['cover_image'] ?? 'placeholder.webp') ?>" 
                                 alt="<?= htmlspecialchars($hit['artist'] . ' - ' . $hit['title']) ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="slider-prev">‹</button>
            <button class="slider-next">›</button>
        </div>
    </section>

    <!-- Новые поступления -->
    <section class="new-arrivals section">
        <div class="container">
            <div class="section-header">
                <h2>🎵 Новые поступления</h2>
                <a href="/catalog" class="link-all">Смотреть все →</a>
            </div>
            <div class="vinyl-grid" id="catalog-container">
                <?php foreach ($newReleases as $item): ?>
                <div class="vinyl-card">
                    <div class="vinyl-card__image">
                        <img src="/uploads/covers/<?= htmlspecialchars($item['cover_image'] ?? 'placeholder.webp') ?>" 
                             alt="<?= htmlspecialchars($item['artist'] . ' - ' . $item['title']) ?>"
                             loading="lazy">
                        <?php if ($item['is_preorder']): ?>
                            <span class="badge preorder">Предзаказ</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="vinyl-card__artist"><?= htmlspecialchars($item['artist']) ?></h3>
                    <p class="vinyl-card__title"><?= htmlspecialchars($item['title']) ?></p>
                    <p class="vinyl-card__year"><?= $item['year_original'] ?></p>
                    <div class="vinyl-card__price"><?= number_format($item['min_price'], 0, '.', ' ') ?> ₽</div>
                    <button class="btn-quick-view" data-id="<?= $item['id'] ?>">Быстрый просмотр</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Предзаказы с таймером -->
    <section class="preorders section section-dark">
        <div class="container">
            <div class="section-header">
                <h2>⏳ Ожидаемые релизы</h2>
                <span class="subtitle">Предзаказ с доставкой после выхода</span>
            </div>
            <div class="preorders-grid">
                <?php foreach ($preorders as $preorder): ?>
                <div class="preorder-card" data-release-date="<?= $preorder['preorder_release_date'] ?>">
                    <div class="preorder-card__image">
                        <img src="/uploads/covers/<?= htmlspecialchars($preorder['cover_image'] ?? 'placeholder.webp') ?>" 
                             alt="<?= htmlspecialchars($preorder['artist'] . ' - ' . $preorder['title']) ?>">
                    </div>
                    <div class="preorder-card__info">
                        <h3><?= htmlspecialchars($preorder['artist']) ?> – <?= htmlspecialchars($preorder['title']) ?></h3>
                        <p class="preorder-type">
                            <?php if ($preorder['vinyl_type'] === 'colored'): ?>
                                🎨 Цветной винил
                            <?php elseif ($preorder['vinyl_type'] === '180g'): ?>
                                ⚡ 180 грамм
                            <?php else: ?>
                                💿 Обычный
                            <?php endif; ?>
                        </p>
                        <div class="preorder-timer" data-date="<?= $preorder['preorder_release_date'] ?>">
                            <div class="timer-block"><span class="timer-days">--</span><span>дней</span></div>
                            <div class="timer-block"><span class="timer-hours">--</span><span>часов</span></div>
                            <div class="timer-block"><span class="timer-minutes">--</span><span>минут</span></div>
                        </div>
                        <div class="preorder-price"><?= number_format($preorder['price'], 0, '.', ' ') ?> ₽</div>
                        <button class="btn-preorder" data-pressing-id="<?= $preorder['pressing_id'] ?>">Предзаказать</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Жанры -->
    <section class="genres section">
        <div class="container">
            <h2>📀 Исследуйте по жанрам</h2>
            <div class="genres-grid">
                <?php foreach ($genres as $genre): ?>
                <a href="/catalog?genres=<?= strtolower($genre) ?>" class="genre-card">
                    <div class="genre-card__icon"><?= match($genre) {
                        'Rock' => '🎸',
                        'Jazz' => '🎷',
                        'Electronic' => '🎛️',
                        'Classical' => '🎻',
                        default => '🎵'
                    } ?></div>
                    <h3><?= $genre ?></h3>
                    <p>Коллекция винила</p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Тест Уровень виниломана -->
    <section class="vinyl-quiz section section-accent">
        <div class="container">
            <div class="quiz-container">
                <div class="quiz-content">
                    <h2>🔍 Уровень виниломана</h2>
                    <p>Пройдите тест из 5 вопросов — подберем пластинку под ваш бюджет и вкус!</p>
                    <button class="btn-quiz-start" id="startQuizBtn" onclick="alert('🎵 Тест появится в следующей версии! А пока — наслаждайтесь подборкой!')">Начать тест →</button>
                </div>
                <div class="quiz-visual">
                    <svg width="180" height="180" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="90" stroke="#d87c3c" stroke-width="4" fill="#1a1a1a"/>
                        <circle cx="100" cy="100" r="60" stroke="#d87c3c" stroke-width="2" fill="none"/>
                        <circle cx="100" cy="100" r="15" fill="#d87c3c"/>
                        <line x1="100" y1="100" x2="160" y2="60" stroke="#d87c3c" stroke-width="3"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="/js/slider.js"></script>
<script src="/js/preorder-timer.js"></script>
<script src="/js/cart.js"></script>

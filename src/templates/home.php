<?php ob_start(); ?>

<div class="container py-5">
    <!-- Главный блок с заголовком -->
    <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-8">
            <h1 class="gradient-heading fw-bold mb-4" style="font-size: 2.8rem; line-height: 1.2;">Откройте для себя совместимость с партнером</h1>
            <p class="lead mb-5" style="font-size: 1.25rem; color: #555;">Пройдите тест на сексуальные предпочтения, поделитесь ссылкой с партнером и узнайте ваши совпадения</p>
        </div>
    </div>

    <!-- Карточки с преимуществами в современном стиле -->
    <div class="row justify-content-center mb-5 gx-4">
        <!-- Карточка 1 -->
        <div class="col-md-4 mb-4">
            <div class="card card-feature shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <div class="feature-circle-container mb-4">
                        <div class="feature-circle d-flex align-items-center justify-content-center">
                            <span class="feature-number">1</span>
                        </div>
                    </div>
                    <h3 class="feature-title mb-3">100% Анонимно</h3>
                    <p class="feature-text text-center">Все ответы хранятся только на вашем устройстве</p>
                </div>
            </div>
        </div>
        <!-- Карточка 2 -->
        <div class="col-md-4 mb-4">
            <div class="card card-feature shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <div class="feature-circle-container mb-4">
                        <div class="feature-circle d-flex align-items-center justify-content-center">
                            <span class="feature-number">2</span>
                        </div>
                    </div>
                    <h3 class="feature-title mb-3">5 Минут</h3>
                    <p class="feature-text text-center">Быстрый тест с простыми вариантами ответов</p>
                </div>
            </div>
        </div>

        <!-- Карточка 3 -->
        <div class="col-md-4 mb-4">
            <div class="card card-feature shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <div class="feature-circle-container mb-4">
                        <div class="feature-circle d-flex align-items-center justify-content-center">
                            <span class="feature-number">3</span>
                        </div>
                    </div>
                    <h3 class="feature-title mb-3">Точный Результат</h3>
                    <p class="feature-text text-center">Увидите только взаимные интересы</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Кнопка начать тест (современный стиль) -->
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 text-center">
            <div class="cta-container py-3">
                <a href="/test" class="btn-start-test">
                    Начать тест
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
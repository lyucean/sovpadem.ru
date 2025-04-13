<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="card-title">Добро пожаловать в Совпадём!</h1>
                <p class="card-text">
                    Узнайте о сексуальных предпочтениях друг друга в безопасной и комфортной форме.
                    Пройдите тест, поделитесь ссылкой с партнером и узнайте, где ваши желания совпадают.
                </p>
                <a href="/test" class="btn btn-primary btn-lg mt-3">Пройти тест</a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>
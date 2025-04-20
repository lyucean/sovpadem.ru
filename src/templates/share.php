<?php ob_start(); ?>

<div class="test-container">
    <div class="test-card">
        <h1 class="test-title">Поделитесь тестом с партнером</h1>
        
        <div class="share-section">
            <p class="share-text">Спасибо за прохождение теста! Теперь поделитесь этой ссылкой с вашим партнером:</p>
            
            <div class="share-link-container">
                <input type="text" id="shareLink" class="share-link-input" value="<?= "http://" . $_SERVER['HTTP_HOST'] . "/test/" . htmlspecialchars($shareId) ?>" readonly>
                <button class="share-button" id="copyBtn">Копировать</button>
            </div>
            
            <p class="share-text mt-4">После того, как ваш партнер пройдет тест, вы оба сможете увидеть результаты по этой ссылке:</p>
            
            <div class="share-link-container">
                <input type="text" id="resultsLink" class="share-link-input" value="<?= "http://" . $_SERVER['HTTP_HOST'] . "/results/" . htmlspecialchars($shareId) ?>" readonly>
                <button class="share-button" id="copyResultsBtn">Копировать</button>
            </div>
            
            <div class="share-note">
                <strong>Важно!</strong> Сохраните эти ссылки, чтобы не потерять доступ к результатам.
            </div>
            
            <div class="privacy-note">
                <strong>Конфиденциальность:</strong> Вы и ваш партнер увидите только те желания, которые совпали у вас обоих. Все остальные предпочтения останутся тайными.
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Поделитесь тестом - Совпадём';
include 'layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('copyBtn').addEventListener('click', function() {
        const shareLink = document.getElementById('shareLink');
        shareLink.select();
        document.execCommand('copy');
        this.textContent = 'Скопировано!';
        setTimeout(() => this.textContent = 'Копировать', 2000);
    });
    
    document.getElementById('copyResultsBtn').addEventListener('click', function() {
        const resultsLink = document.getElementById('resultsLink');
        resultsLink.select();
        document.execCommand('copy');
        this.textContent = 'Скопировано!';
        setTimeout(() => this.textContent = 'Копировать', 2000);
    });
});
</script>
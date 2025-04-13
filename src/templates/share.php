<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Поделитесь тестом с партнером</h2>
            </div>
            <div class="card-body text-center">
                <p>Спасибо за прохождение теста! Теперь поделитесь этой ссылкой с вашим партнером:</p>
                
                <div class="input-group mb-3">
                    <input type="text" id="shareLink" class="form-control" value="<?= "http://" . $_SERVER['HTTP_HOST'] . "/test/" . htmlspecialchars($shareId) ?>" readonly>
                    <button class="btn btn-outline-primary" type="button" id="copyBtn">Копировать</button>
                </div>
                
                <p class="mt-4">После того, как ваш партнер пройдет тест, вы оба сможете увидеть результаты по этой ссылке:</p>
                
                <div class="input-group mb-3">
                    <input type="text" id="resultsLink" class="form-control" value="<?= "http://" . $_SERVER['HTTP_HOST'] . "/results/" . htmlspecialchars($shareId) ?>" readonly>
                    <button class="btn btn-outline-primary" type="button" id="copyResultsBtn">Копировать</button>
                </div>
                
                <div class="alert alert-info mt-4">
                    <strong>Важно!</strong> Сохраните эти ссылки, чтобы не потерять доступ к результатам.
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include 'layout.php';
?>

<!-- Перемещаем скрипт после включения layout.php, чтобы jQuery был уже загружен -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#copyBtn').click(function() {
        const shareLink = document.getElementById('shareLink');
        shareLink.select();
        document.execCommand('copy');
        $(this).text('Скопировано!');
        setTimeout(() => $(this).text('Копировать'), 2000);
    });
    
    $('#copyResultsBtn').click(function() {
        const resultsLink = document.getElementById('resultsLink');
        resultsLink.select();
        document.execCommand('copy');
        $(this).text('Скопировано!');
        setTimeout(() => $(this).text('Копировать'), 2000);
    });
});
</script>
<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Результаты теста</h2>
            </div>
            <div class="card-body">
                <div id="resultsStatus" class="alert alert-info">
                    Загрузка результатов...
                </div>
                
                <div id="resultsContainer" style="display: none;">
                    <h3 class="mb-4">Ваши совпадения</h3>
                    <div id="matchesList" class="list-group mb-4">
                        <!-- Matches will be displayed here -->
                    </div>
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
    // Получаем данные из PHP переменных
    const testId = '<?= $testId ?>';
    const results = <?= json_encode($results) ?>;
    
    // Проверяем, прошли ли оба партнера тест
    const bothCompleted = results.completed; // Исправлено с bothCompleted на completed

    if (!bothCompleted) {
        $('#resultsStatus').removeClass('alert-info').addClass('alert-warning')
            .html('Ожидание прохождения теста партнером. Результаты будут доступны, когда оба партнера пройдут тест.');
        return;
    }
    
    // Отображаем совпадения
    let matchesHtml = '';
    if (results.matches && results.matches.length > 0) {
        results.matches.forEach(match => {
            matchesHtml += `
                <div class="list-group-item">
                    <h5 class="mb-2">${match.question}</h5>
                    <div class="d-flex justify-content-between">
                        <span>Вы: <strong>${match.creator_value}</strong></span>
                        <span>Партнер: <strong>${match.partner_value}</strong></span>
                    </div>
                </div>
            `;
        });
    } else {
        matchesHtml = '<div class="alert alert-warning">Совпадений не найдено. Попробуйте обсудить ваши предпочтения.</div>';
    }
    
    $('#matchesList').html(matchesHtml);
    $('#resultsStatus').hide();
    $('#resultsContainer').show();
});
</script>
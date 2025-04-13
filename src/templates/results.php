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
    // In a real application, this would fetch data from the server
    // For demo purposes, we'll simulate some results
    
    const testId = '<?= $testId ?>';
    
    // Simulate API call
    setTimeout(function() {
        // Check if both partners have completed the test
        const bothCompleted = true; // This would be determined by the server
        
        if (!bothCompleted) {
            $('#resultsStatus').removeClass('alert-info').addClass('alert-warning')
                .html('Ожидание прохождения теста партнером. Результаты будут доступны, когда оба партнера пройдут тест.');
            return;
        }
        
        // Sample matching results
        const matches = [
            {
                question: "Вы бы хотели попробовать ролевые игры?",
                person1Answer: "Да",
                person2Answer: "Если партнер хочет"
            },
            {
                question: "Вам интересен оральный секс?",
                person1Answer: "Конечно да",
                person2Answer: "Да"
            },
            {
                question: "Вы бы хотели использовать игрушки во время секса?",
                person1Answer: "Если партнер хочет",
                person2Answer: "Да"
            }
        ];
        
        // Display matches
        let matchesHtml = '';
        if (matches.length > 0) {
            matches.forEach(match => {
                matchesHtml += `
                    <div class="list-group-item">
                        <h5 class="mb-2">${match.question}</h5>
                        <div class="d-flex justify-content-between">
                            <span>Вы: <strong>${match.person1Answer}</strong></span>
                            <span>Партнер: <strong>${match.person2Answer}</strong></span>
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
    }, 1000);
});
</script>
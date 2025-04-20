<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Тест на совместимость</h2>
            </div>
            <div class="card-body">
                <form id="compatibilityTest" action="/submit-test" method="post">
                    <div id="questionsContainer">
                        <!-- Questions will be loaded here via JavaScript -->
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" id="prevBtn" class="btn btn-secondary" disabled>Назад</button>
                        <button type="button" id="skipBtn" class="btn btn-outline-secondary">Пропустить</button>
                        <button type="button" id="nextBtn" class="btn btn-primary">Далее</button>
                        <button type="submit" id="submitBtn" class="btn btn-success" style="display: none;">Завершить</button>
                    </div>
                </form>
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
    // Загружаем вопросы из PHP
    const questions = <?= json_encode(array_column($questions, 'text')) ?>;
    const questionIds = <?= json_encode(array_column($questions, 'id')) ?>;
    const answerOptions = <?= json_encode($answerOptions) ?>;
    
    let currentQuestion = 0;
    const answers = {};
    
    function renderQuestion(index) {
        if (index >= questions.length) {
            return false;
        }
        
        const questionHtml = `
            <div class="question mb-4">
                <h4 class="mb-3">${index + 1}. ${questions[index]}</h4>
                <div class="options">
                    ${answerOptions.map(option => `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="q${index}" id="q${index}_${option.value}" value="${option.value}" ${answers[questionIds[index]] === option.value ? 'checked' : ''}>
                            <label class="form-check-label" for="q${index}_${option.value}">
                                ${option.text}
                            </label>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        $('#questionsContainer').html(questionHtml);
        
        // Update buttons
        $('#prevBtn').prop('disabled', index === 0);
        
        if (index === questions.length - 1) {
            $('#nextBtn').hide();
            $('#submitBtn').show();
        } else {
            $('#nextBtn').show();
            $('#submitBtn').hide();
        }
        
        return true;
    }
    
    // Initialize first question
    renderQuestion(currentQuestion);
    
    // Next button handler
    $('#nextBtn').click(function() {
        // Save current answer
        const selectedValue = $(`input[name="q${currentQuestion}"]:checked`).val();
        if (selectedValue) {
            // Используем реальный ID вопроса из базы данных
            answers[questionIds[currentQuestion]] = parseInt(selectedValue);
        }
        
        // Move to next question
        currentQuestion++;
        renderQuestion(currentQuestion);
    });
    
    // Previous button handler
    $('#prevBtn').click(function() {
        currentQuestion--;
        renderQuestion(currentQuestion);
    });
    
    // Skip button handler
    $('#skipBtn').click(function() {
        // Move to next question without saving
        currentQuestion++;
        renderQuestion(currentQuestion);
    });
    
    // Form submission
    $('#compatibilityTest').submit(function(e) {
        e.preventDefault();
        
        // Save last answer if selected
        const selectedValue = $(`input[name="q${currentQuestion}"]:checked`).val();
        if (selectedValue) {
            // Используем реальный ID вопроса из базы данных
            answers[questionIds[currentQuestion]] = parseInt(selectedValue);
        }
        
        // Prepare data for submission
        const formData = {
            answers: JSON.stringify(answers)
        };
        
        // Make sure this is a POST request
        $.ajax({
            type: 'POST',
            url: '/submit-test',
            data: formData,
            success: function(response) {
                window.location.href = `/share/${response.testId}`;
            },
            error: function(xhr, status, error) {
                console.error("Error submitting test:", error);
                alert("Произошла ошибка при отправке теста. Пожалуйста, попробуйте еще раз.");
            }
        });
    });
});
</script>
<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Тест на совместимость</h2>
                <p class="text-muted">Ваш партнер уже прошел этот тест и ждет ваших ответов</p>
            </div>
            <div class="card-body">
                <form id="partnerCompatibilityTest" action="/submit-partner-test" method="post">
                    <input type="hidden" name="testId" value="<?= htmlspecialchars($testId) ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample questions
    const questions = [
        "Вы бы хотели попробовать ролевые игры?",
        "Вам интересен оральный секс?",
        "Вы бы хотели использовать игрушки во время секса?",
        "Вам нравится идея секса на природе?",
        "Вы бы хотели попробовать БДСМ?",
        // Add more questions as needed
    ];
    
    const answerOptions = [
        { value: 1, text: "Фу" },
        { value: 2, text: "Нет" },
        { value: 3, text: "Если партнер хочет" },
        { value: 4, text: "Да" },
        { value: 5, text: "Конечно да" }
    ];
    
    let currentQuestion = 0;
    const answers = {};
    const testId = $('input[name="testId"]').val();
    
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
                            <input class="form-check-input" type="radio" name="q${index}" id="q${index}_${option.value}" value="${option.value}" ${answers[index] === option.value ? 'checked' : ''}>
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
            answers[currentQuestion] = parseInt(selectedValue);
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
    $('#partnerCompatibilityTest').submit(function(e) {
        e.preventDefault();
        
        // Save last answer if selected
        const selectedValue = $(`input[name="q${currentQuestion}"]:checked`).val();
        if (selectedValue) {
            answers[currentQuestion] = parseInt(selectedValue);
        }
        
        // Prepare data for submission
        const formData = {
            testId: testId,
            answers: JSON.stringify(answers)
        };
        
        // Submit data via AJAX
        $.ajax({
            url: '/submit-partner-test',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    window.location.href = '/results/' + testId;
                } else {
                    alert('Произошла ошибка при обработке ответа сервера.');
                }
            },
            error: function() {
                alert('Произошла ошибка при отправке теста. Пожалуйста, попробуйте еще раз.');
            }
        });
    });
});
</script>
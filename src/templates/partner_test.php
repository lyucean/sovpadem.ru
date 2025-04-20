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
$(document).ready(function() {
    // Get questions and their IDs from PHP variables passed from the controller
    const questions = [
        <?php foreach ($questions as $question): ?>
            "<?= htmlspecialchars($question['text']) ?>",
        <?php endforeach; ?>
    ];
    
    // Добавляем массив с ID вопросов
    const questionIds = [
        <?php foreach ($questions as $question): ?>
            <?= $question['id'] ?>,
        <?php endforeach; ?>
    ];
    
    // Get answer options from PHP variables
    const answerOptions = [
        <?php foreach ($answerOptions as $option): ?>
            { value: <?= $option['value'] ?>, text: "<?= htmlspecialchars($option['text']) ?>" },
        <?php endforeach; ?>
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

    // Form submission - Fix the form selector to match the actual form ID
    $('#partnerCompatibilityTest').submit(function(e) {
        e.preventDefault();

        // Save last answer if selected
        const selectedValue = $(`input[name="q${currentQuestion}"]:checked`).val();
        if (selectedValue) {
            // Используем реальный ID вопроса из базы данных
            answers[questionIds[currentQuestion]] = parseInt(selectedValue);
        }

        // Get test ID from the page
        const testId = '<?= $testId ?>';

        // Prepare data for submission
        const formData = {
            testId: testId,
            answers: JSON.stringify(answers)
        };

        // Make sure this is a POST request
        $.ajax({
            type: 'POST',
            url: '/submit-partner-test',
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.href = `/results/${testId}`;
                } else {
                    alert("Произошла ошибка: " + (response.error || "Неизвестная ошибка"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Error submitting partner test:", error);
                alert("Произошла ошибка при отправке теста. Пожалуйста, попробуйте еще раз.");
            }
        });
    });
});
</script>
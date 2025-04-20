<?php ob_start(); ?>

<div class="test-container">
    <div class="test-card">
        <h1 class="test-title">Ваши предпочтения</h1>
        
        <div class="progress-container">
            <div class="progress-info">
                <span>Вопрос <span id="currentQuestionNum">1</span> из <span id="totalQuestions"><?= count($questions) ?></span></span>
                <span><span id="progressPercentage">0</span>% завершено</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressBar" style="width: 0%"></div>
            </div>
        </div>
        
        <div id="questionsContainer" class="question-container">
            <!-- Questions will be loaded here via JavaScript -->
        </div>
        
        <div class="navigation-buttons">
            <button type="button" id="prevBtn" class="nav-button back-button" disabled>Назад</button>
            <button type="button" id="nextBtn" class="nav-button next-button">Дальше <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg></button>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$title = 'Тест на совместимость';
include 'layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Загружаем вопросы из PHP
    const questions = <?= json_encode(array_column($questions, 'text')) ?>;
    const questionIds = <?= json_encode(array_column($questions, 'id')) ?>;
    const answerOptions = <?= json_encode($answerOptions) ?>;
    
    let currentQuestion = 0;
    const answers = {};
    const totalQuestions = questions.length;
    
    function updateProgress() {
        const progressPercentage = Math.round((currentQuestion / totalQuestions) * 100);
        document.getElementById('currentQuestionNum').textContent = currentQuestion + 1;
        document.getElementById('progressPercentage').textContent = progressPercentage;
        document.getElementById('progressBar').style.width = progressPercentage + '%';
    }
    
    function renderQuestion(index) {
        if (index >= questions.length) {
            return false;
        }
        
        const questionHtml = `
            <h2 class="question-text">${questions[index]}</h2>
            <form id="answerForm">
                ${answerOptions.map(option => `
                    <button type="button" class="answer-button answer-level-${option.value}" data-value="${option.value}">
                        ${option.text}
                    </button>
                `).join('')}
                <button type="button" class="skip-button" id="skipBtn">Пропустить</button>
            </form>
        `;
        
        document.getElementById('questionsContainer').innerHTML = questionHtml;
        
        // Add event listeners to answer buttons
        document.querySelectorAll('.answer-button').forEach(button => {
            button.addEventListener('click', function() {
                const value = parseInt(this.getAttribute('data-value'));
                answers[questionIds[currentQuestion]] = value;
                moveToNextQuestion();
            });
        });
        
        // Skip button handler
        document.getElementById('skipBtn').addEventListener('click', function() {
            moveToNextQuestion();
        });
        
        // Update buttons
        document.getElementById('prevBtn').disabled = index === 0;
        
        if (index === questions.length - 1) {
            document.getElementById('nextBtn').textContent = 'Завершить';
            document.getElementById('nextBtn').classList.add('submit-button');
        } else {
            document.getElementById('nextBtn').innerHTML = 'Дальше <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg>';
            document.getElementById('nextBtn').classList.remove('submit-button');
        }
        
        updateProgress();
        return true;
    }
    
    function moveToNextQuestion() {
        currentQuestion++;
        if (currentQuestion >= questions.length) {
            submitTest();
        } else {
            renderQuestion(currentQuestion);
        }
    }
    
    function moveToPrevQuestion() {
        currentQuestion--;
        renderQuestion(currentQuestion);
    }
    
    function submitTest() {
        // Prepare data for submission
        const formData = {
            answers: JSON.stringify(answers)
        };
        
        // Make sure this is a POST request
        fetch('/submit-test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            window.location.href = `/share/${data.testId}`;
        })
        .catch(error => {
            console.error("Error submitting test:", error);
            alert("Произошла ошибка при отправке теста. Пожалуйста, попробуйте еще раз.");
        });
    }
    
    // Initialize first question
    renderQuestion(currentQuestion);
    
    // Next button handler
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (currentQuestion === questions.length - 1) {
            submitTest();
        } else {
            moveToNextQuestion();
        }
    });
    
    // Previous button handler
    document.getElementById('prevBtn').addEventListener('click', function() {
        moveToPrevQuestion();
    });
});
</script>
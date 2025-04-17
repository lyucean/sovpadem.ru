<?php

// Загружаем переменные окружения из файла .env
require_once __DIR__ . '/env_loader.php';

class Database {
    private $pdo;
    
    public function __construct() {
        // Определяем хост базы данных в зависимости от окружения
        $host = getenv('MYSQL_HOST');
        if (!$host) {
            // Проверяем, в каком окружении запущено приложение
            $host = getenv('APP_ENV') === 'prod' ? 'db' : 'db_dev';
        }
        
        $dbname = getenv('MYSQL_DATABASE') ?: 'sovpadem';
        $username = getenv('MYSQL_USER') ?: 'sovpadem_user';
        $password = getenv('MYSQL_PASSWORD') ?: 'sovpadem_password';
        
        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    // Добавляем явные настройки кодировки
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    
    // Получить все вопросы
    public function getQuestions() {
        $stmt = $this->pdo->query("SELECT * FROM questions WHERE active = TRUE ORDER BY id");
        return $stmt->fetchAll();
    }
    
    // Получить все варианты ответов
    public function getAnswerOptions() {
        $stmt = $this->pdo->query("SELECT * FROM answer_options ORDER BY value");
        return $stmt->fetchAll();
    }
    
    // Создать новый тест
    public function createTest($id) {
        $stmt = $this->pdo->prepare("INSERT INTO tests (id) VALUES (?)");
        return $stmt->execute([$id]);
    }
    
    // Сохранить ответы пользователя
    public function saveUserAnswers($testId, $answers, $isPartner = false) {
        // Convert $isPartner to integer (0 or 1)
        $isPartnerInt = $isPartner ? 1 : 0;
        
        $stmt = $this->pdo->prepare("
            INSERT INTO user_answers (test_id, question_id, answer_value, is_partner) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($answers as $questionId => $answerValue) {
            $stmt->execute([$testId, $questionId + 1, $answerValue, $isPartnerInt]);
        }
        
        // Обновить статус теста
        if ($isPartner) {
            $this->pdo->prepare("UPDATE tests SET partner_completed = TRUE WHERE id = ?")->execute([$testId]);
        }
        
        return true;
    }
    
    // Получить результаты теста
    public function getTestResults($testId) {
        // Проверяем, прошли ли оба партнера тест
        $stmt = $this->pdo->prepare("
            SELECT creator_completed, partner_completed 
            FROM tests 
            WHERE id = ?
        ");
        $stmt->execute([$testId]);
        $test = $stmt->fetch();
        
        if (!$test || !$test['creator_completed'] || !$test['partner_completed']) {
            return ['completed' => false];
        }
        
        // Получаем ответы создателя
        $stmt = $this->pdo->prepare("
            SELECT question_id, answer_value 
            FROM user_answers 
            WHERE test_id = ? AND is_partner = FALSE
        ");
        $stmt->execute([$testId]);
        $creatorAnswers = [];
        foreach ($stmt->fetchAll() as $row) {
            $creatorAnswers[$row['question_id']] = $row['answer_value'];
        }
        
        // Получаем ответы партнера
        $stmt = $this->pdo->prepare("
            SELECT question_id, answer_value 
            FROM user_answers 
            WHERE test_id = ? AND is_partner = TRUE
        ");
        $stmt->execute([$testId]);
        $partnerAnswers = [];
        foreach ($stmt->fetchAll() as $row) {
            $partnerAnswers[$row['question_id']] = $row['answer_value'];
        }
        
        // Получаем вопросы
        $stmt = $this->pdo->prepare("
            SELECT id, text 
            FROM questions 
            WHERE id IN (SELECT DISTINCT question_id FROM user_answers WHERE test_id = ?)
        ");
        $stmt->execute([$testId]);
        $questions = [];
        foreach ($stmt->fetchAll() as $row) {
            $questions[$row['id']] = $row['text'];
        }
        
        // Анализируем совпадения
        $matches = [];
        foreach ($creatorAnswers as $questionId => $creatorValue) {
            if (isset($partnerAnswers[$questionId])) {
                $partnerValue = $partnerAnswers[$questionId];
                
                // Считаем совпадением, если оба ответа положительные (4 или 5)
                if ($creatorValue >= 4 && $partnerValue >= 4) {
                    $matches[] = [
                        'question' => $questions[$questionId],
                        'creator_value' => $creatorValue,
                        'partner_value' => $partnerValue
                    ];
                }
            }
        }
        
        return [
            'completed' => true,
            'matches' => $matches
        ];
    }
    
    // Проверить существование теста
    public function testExists($testId) {
        $stmt = $this->pdo->prepare("SELECT id FROM tests WHERE id = ?");
        $stmt->execute([$testId]);
        return $stmt->fetch() !== false;
    }
}
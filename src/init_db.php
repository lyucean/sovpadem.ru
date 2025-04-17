<?php

// Загружаем переменные окружения из файла .env
require_once __DIR__ . '/env_loader.php';

// Database connection parameters
$host = getenv('MYSQL_HOST') ?: 'db_dev';
$dbname = getenv('MYSQL_DATABASE') ?: 'sovpadem';
$username = getenv('MYSQL_USER') ?: 'sovpadem_user';
$password = getenv('MYSQL_PASSWORD') ?: 'sovpadem_password';

try {
    // Connect to the database with explicit UTF-8 settings
    $pdo = new PDO(
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
    
    echo "Connected to database successfully.\n";
    
    // Create questions table with explicit UTF-8 settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            text VARCHAR(255) NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created questions table.\n";
    
    // Create answer_options table with explicit UTF-8 settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS answer_options (
            id INT AUTO_INCREMENT PRIMARY KEY,
            value INT NOT NULL,
            text VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created answer_options table.\n";
    
    // Create tests table with explicit UTF-8 settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tests (
            id VARCHAR(16) PRIMARY KEY,
            creator_completed BOOLEAN DEFAULT TRUE,
            partner_completed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created tests table.\n";
    
    // Create user_answers table with explicit UTF-8 settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_answers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            test_id VARCHAR(16) NOT NULL,
            question_id INT NOT NULL,
            answer_value INT NOT NULL,
            is_partner BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (test_id) REFERENCES tests(id),
            FOREIGN KEY (question_id) REFERENCES questions(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created user_answers table.\n";
    
    // Check if questions table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert sample questions
        $pdo->exec("
            INSERT INTO questions (text) VALUES
            ('Тестовый вопрос номер 1'),
            ('Тестовый вопрос номер 2'),
            ('Тестовый вопрос номер 3'),
            ('Тестовый вопрос номер 4'),
            ('Тестовый вопрос номер 5')
        ");
        echo "Inserted sample questions.\n";
    }
    
    // Check if answer_options table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM answer_options");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert answer options
        $pdo->exec("
            INSERT INTO answer_options (value, text) VALUES
            (1, 'Фу'),
            (2, 'Нет'),
            (3, 'Если партнер хочет'),
            (4, 'Да'),
            (5, 'Конечно да')
        ");
        echo "Inserted answer options.\n";
    }
    
    echo "Database initialization completed successfully.\n";
    
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}
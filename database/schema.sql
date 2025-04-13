-- Таблица вопросов
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица вариантов ответов
CREATE TABLE IF NOT EXISTS answer_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value INT NOT NULL,
    text VARCHAR(100) NOT NULL
);

-- Таблица тестов
CREATE TABLE IF NOT EXISTS tests (
    id VARCHAR(16) PRIMARY KEY,
    creator_completed BOOLEAN DEFAULT TRUE,
    partner_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица ответов пользователей
CREATE TABLE IF NOT EXISTS user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id VARCHAR(16) NOT NULL,
    question_id INT NOT NULL,
    answer_value INT NOT NULL,
    is_partner BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Проверяем, есть ли уже данные в таблице вопросов
INSERT INTO questions (text)
SELECT * FROM (
    SELECT 'Тестовый вопрос номер 1' AS text
    UNION SELECT 'Тестовый вопрос номер 2'
    UNION SELECT 'Тестовый вопрос номер 3'
    UNION SELECT 'Тестовый вопрос номер 4'
    UNION SELECT 'Тестовый вопрос номер 5'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM questions LIMIT 1
);

-- Проверяем, есть ли уже данные в таблице вариантов ответов
INSERT INTO answer_options (value, text)
SELECT * FROM (
    SELECT 1 AS value, 'Фу' AS text
    UNION SELECT 2, 'Нет'
    UNION SELECT 3, 'Если партнер хочет'
    UNION SELECT 4, 'Да'
    UNION SELECT 5, 'Конечно да'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM answer_options LIMIT 1
);
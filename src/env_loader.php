<?php

/**
 * Загружает переменные окружения из файла .env в корне проекта
 */
function load_env_file() {
    // Путь к файлу .env в корне проекта
    $env_file = dirname(__DIR__) . '/.env';
    
    // Проверяем существование файла
    if (file_exists($env_file)) {
        // Загружаем файл .env как INI файл
        $env_vars = parse_ini_file($env_file);

        // Устанавливаем переменные окружения
        if ($env_vars) {
            foreach ($env_vars as $key => $value) {
                if (!getenv($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

// Загружаем переменные окружения при подключении файла
load_env_file();
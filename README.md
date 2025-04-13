# Совпадём - Тест на сексуальную совместимость

Проект "Совпадём" - это веб-приложение, которое позволяет парам анонимно узнать о сексуальных предпочтениях друг друга в безопасной и комфортной форме.

## Описание

Приложение предлагает пользователям пройти тест, состоящий из вопросов о сексуальных предпочтениях. После прохождения теста пользователь получает уникальную ссылку, которой может поделиться с партнером. Когда партнер также проходит тест, оба участника получают доступ к результатам, которые показывают только совпадающие предпочтения.

## Технологии

- PHP 8.2
- MySQL 8.0
- Apache
- Docker
- jQuery
- Bootstrap 5

## Установка и запуск

### Требования

- Docker
- Docker Compose

### Шаги по установке

1. Клонируйте репозиторий:
```bash
git clone https://github.com/yourusername/sovpadem.git
cd sovpadem
```

2. Запустите контейнеры:
```bash
docker compose up -d
```

3. Приложение будет доступно по адресу: http://localhost:8080

```markdown
sovpadem/
├── docker/
│   └── apache/
│       └── 000-default.conf
├── src/
│   ├── public/
│   │   ├── css/
│   │   │   └── styles.css
│   │   ├── js/
│   │   │   └── app.js
│   │   └── index.php
│   └── templates/
│       ├── home.php
│       ├── layout.php
│       ├── partner_test.php
│       ├── results.php
│       ├── share.php
│       └── test.php
├── vendor/
├── .gitignore
├── composer.json
├── docker-compose.yml
└── README.md
```

## Функциональность
- Прохождение теста на сексуальные предпочтения
- Генерация уникальной ссылки для партнера
- Анонимное сравнение результатов
- Отображение только совпадающих предпочтений
## База данных
Приложение использует MySQL для хранения данных. Схема базы данных включает таблицы для хранения тестов, ответов пользователей и результатов.

## Безопасность
Все данные хранятся анонимно, без привязки к личной информации пользователей. Доступ к результатам возможен только по уникальным ссылкам.

## Лицензия
MIT

## Контакты
Для вопросов и предложений: https://lyucean.com
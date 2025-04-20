<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../Database.php'; // Добавляем подключение класса Database

// Create Container
$container = new Container();
$container->set('renderer', function() {
    return new PhpRenderer(__DIR__ . '/../templates');
});

// Add Database to container
$container->set('db', function() {
    return new Database();
});

// Create App
$app = AppFactory::createFromContainer($container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Define routes
$app->get('/', function (Request $request, Response $response) {
    return $this->get('renderer')->render($response, 'home.php', [
        'title' => 'Совпадём - тест на совместимость'
    ]);
});

$app->get('/test', function (Request $request, Response $response) {
    $questions = $this->get('db')->getQuestions();
    $answerOptions = $this->get('db')->getAnswerOptions();
    
    return $this->get('renderer')->render($response, 'test.php', [
        'title' => 'Пройти тест',
        'questions' => $questions,
        'answerOptions' => $answerOptions
    ]);
});

// Добавьте этот маршрут для обработки GET-запросов к /submit-test
$app->get('/submit-test', function (Request $request, Response $response) {
    // Перенаправляем на страницу теста
    return $response->withHeader('Location', '/test')->withStatus(302);
});
// Make sure this route accepts POST requests
$app->post('/submit-test', function (Request $request, Response $response) {
    // Process test submission
    $data = $request->getParsedBody();
    $answers = json_decode($data['answers'] ?? '{}', true);
    
    // Generate unique link
    $uniqueId = bin2hex(random_bytes(8));

    // Save test to database
    $db = $this->get('db');
    $db->createTest($uniqueId);
    $db->saveUserAnswers($uniqueId, $answers);

    // Return JSON response with the test ID
    $response->getBody()->write(json_encode(['testId' => $uniqueId]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/share/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    return $this->get('renderer')->render($response, 'share.php', [
        'title' => 'Поделиться тестом',
        'shareId' => $id
    ]);
});

$app->get('/results/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $db = $this->get('db');

    // Проверяем статус теста
    $testStatus = $db->getTestStatus($id);
    // Если тест не найден, показываем ошибку
    if (!$testStatus) {
        // Если у вас нет шаблона error.php, можно использовать простой текст
        $response->getBody()->write("Ошибка: Тест не найден");
        return $response->withStatus(404);
    }

    // Если партнер еще не прошел тест, перенаправляем на страницу теста для партнера
    if (!$testStatus['partner_completed']) {
        return $response->withHeader('Location', '/test/' . $id)->withStatus(302);
    }

    // Если тест завершен обоими партнерами, показываем результаты
    $results = $db->getTestResults($id);

    return $this->get('renderer')->render($response, 'results.php', [
        'title' => 'Результаты теста',
        'testId' => $id,
        'results' => $results
    ]);
});

// Make sure this route accepts POST requests
$app->post('/submit-partner-test', function (Request $request, Response $response) {
    // Process partner test submission
    $data = $request->getParsedBody();
    $testId = $data['testId'] ?? '';
    $answers = json_decode($data['answers'] ?? '{}', true);

    // Save partner answers to database
    $db = $this->get('db');
    if ($db->testExists($testId)) {
        $db->saveUserAnswers($testId, $answers, true);
        $response->getBody()->write(json_encode(['success' => true]));
    } else {
        $response->getBody()->write(json_encode(['success' => false, 'error' => 'Test not found']));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// Add this route for partner test
$app->get('/test/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $questions = $this->get('db')->getQuestions();
    $answerOptions = $this->get('db')->getAnswerOptions();

    return $this->get('renderer')->render($response, 'partner_test.php', [
        'title' => 'Пройти тест партнера',
        'testId' => $id,
        'questions' => $questions,
        'answerOptions' => $answerOptions
    ]);
});

// Run app
$app->run();
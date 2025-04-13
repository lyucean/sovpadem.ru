<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;

require __DIR__ . '/../../vendor/autoload.php';

// Create Container
$container = new Container();
$container->set('renderer', function() {
    return new PhpRenderer(__DIR__ . '/../templates');
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
    return $this->get('renderer')->render($response, 'test.php', [
        'title' => 'Пройти тест'
    ]);
});

$app->post('/submit-test', function (Request $request, Response $response) {
    // Process test submission
    $data = $request->getParsedBody();
    
    // Generate unique link
    $uniqueId = bin2hex(random_bytes(8));
    
    // Save answers to database
    // TODO: Implement database storage
    
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
    // TODO: Fetch results from database
    
    return $this->get('renderer')->render($response, 'results.php', [
        'title' => 'Результаты теста',
        'testId' => $id
    ]);
});

// Обработка ответов партнера
$app->post('/submit-partner-test', function (Request $request, Response $response) {
    // Process partner test submission
    $data = $request->getParsedBody();
    $testId = $data['testId'] ?? '';
    
    // Save partner answers to database
    // TODO: Implement database storage
    
    // Return success response
    $response->getBody()->write(json_encode(['success' => true]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Добавляем маршрут для прохождения теста партнером
$app->get('/test/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    return $this->get('renderer')->render($response, 'partner_test.php', [
        'title' => 'Пройти тест партнера',
        'testId' => $id
    ]);
});

// Run app
$app->run();
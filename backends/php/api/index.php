<?php
require_once __DIR__ . '/../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

//// Инициализация
try {
  $db = new App\Database();
  // $rabbitmq = new App\RabbitMQClient();
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Service initialization failed: ' . $e->getMessage()]);
  exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

try {
  switch (true) {
    case $path === '/api/health' && $method === 'GET':
      $status = [
        'status' => 'healthy',
        'backend' => 'php',
        'timestamp' => time(),
        // 'rabbitmq' => $rabbitmq->isConnected() ? 'connected' : 'disconnected'
      ];
      echo json_encode($status);
      break;

    case $path === '/api/users' && $method === 'GET':
      $stmt = $db->query("SELECT * FROM users ORDER BY id");
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($users);
      break;

    case $path === '/api/users' && $method === 'POST':
      $input = json_decode(file_get_contents('php://input'), true);

      if (!isset($input['name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and email are required']);
        break;
      }

//      // Отправляем задачу в очередь для асинхронной обработки
//      $rabbitmq->publish('user_created', [
//        'name' => $input['name'],
//        'email' => $input['email'],
//        'created_at' => time()
//      ]);

      echo json_encode([
        'message' => 'User creation queued for processing',
        'queue' => 'user_created'
      ]);
      break;

    case $path === '/api/tasks' && $method === 'POST':
      $input = json_decode(file_get_contents('php://input'), true);

//      $rabbitmq->publish('background_tasks', [
//        'type' => $input['type'] ?? 'default',
//        'data' => $input['data'] ?? [],
//        'priority' => $input['priority'] ?? 'normal',
//        'timestamp' => time()
//      ]);

      echo json_encode([
        'message' => 'Task queued for background processing',
        'queue' => 'background_tasks'
      ]);
      break;

    case $path === '/api/notifications' && $method === 'POST':
      $input = json_decode(file_get_contents('php://input'), true);

//      // Используем exchange для разных типов уведомлений
//      $rabbitmq->publishToExchange(
//        'notifications',
//        $input['type'] ?? 'general',
//        [
//          'title' => $input['title'],
//          'message' => $input['message'],
//          'recipients' => $input['recipients'] ?? [],
//          'timestamp' => time()
//        ]
//      );

      echo json_encode(['message' => 'Notification sent']);
      break;

    case $path === '/api/rabbitmq/status' && $method === 'GET':
      echo json_encode([
        // 'connected' => $rabbitmq->isConnected(),
        'connected' => '?',
        'service' => 'rabbitmq'
      ]);
      break;

    default:
      http_response_code(404);
      echo json_encode(['error' => 'Endpoint not found!']);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}

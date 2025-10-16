<?php
require_once __DIR__ . '/../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

//// Init
try {
  $db = new App\Database();
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
      ];
      echo json_encode($status);
      break;

    case $path === '/api/enum' && $method === 'GET':
      echo json_encode([
        'option 1',
        'option 2',
        'option 3',
      ]);
      break;

    case $path === '/api/list' && $method === 'GET':
      echo json_encode([
        'element 1',
        'element 2',
        'element 3',
      ]);
      break;

    case $path === '/api/install' && $method === 'POST':
      $input = json_decode(file_get_contents('php://input'), true);

      echo json_encode([
        'message' => 'All success'
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

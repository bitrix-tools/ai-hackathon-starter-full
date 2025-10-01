<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit(0);
}

require_once '../src/Database.php';
require_once '../src/UserController.php';

$database = new Database();
$userController = new UserController($database);

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Маршрутизация
switch (true) {
  case $path === '/api/health' && $method === 'GET':
    echo json_encode([
      'status' => 'healthy',
      'backend' => 'php',
      'timestamp' => time()
    ]);
  break;

  case $path === '/api/users' && $method === 'GET':
    $userController->getUsers();
  break;

  case $path === '/api/users' && $method === 'POST':
    $input = json_decode(file_get_contents('php://input'), true);
    $userController->createUser($input);
  break;

  case $path === '/' && $method === 'GET':
    echo json_encode(['message' => 'PHP Backend is running']);
  break;

  default:
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
  break;
}

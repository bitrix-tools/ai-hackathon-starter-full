<?php
require_once __DIR__ . '/../vendor/autoload.php';

echo "Starting PHP Worker...\n";

// Инициализация
try {
  $db = new App\Database();
  $rabbitmq = new App\RabbitMQClient();

  if (!$rabbitmq->isConnected()) {
    throw new Exception("Failed to connect to RabbitMQ");
  }

  echo "Worker initialized successfully\n";
  echo "Database: connected\n";
  echo "RabbitMQ: connected\n";

} catch (Exception $e) {
  echo "Worker initialization failed: " . $e->getMessage() . "\n";
  exit(1);
}

// Обработчик для создания пользователей
$userCreatedHandler = function($data) use ($db) {
  echo "Processing user creation: " . $data['email'] . "\n";

  try {
    // Имитируем обработку
    sleep(1);

    $stmt = $db->query(
      "INSERT INTO users (name, email) VALUES (?, ?) RETURNING *",
      [$data['name'], $data['email']]
    );

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "User created successfully: " . $user['email'] . " (ID: " . $user['id'] . ")\n";

    return true;

  } catch (Exception $e) {
    echo "Error creating user: " . $e->getMessage() . "\n";
    return false;
  }
};

// Обработчик для фоновых задач
$backgroundTaskHandler = function($data) {
  echo "Processing background task: " . $data['type'] . "\n";

  try {
    // Имитация долгой задачи
    $duration = rand(2, 5);
    sleep($duration);

    echo "Background task completed: " . $data['type'] . " (took {$duration}s)\n";

    return true;

  } catch (Exception $e) {
    echo "Error processing background task: " . $e->getMessage() . "\n";
    return false;
  }
};

// Основной цикл воркера
while (true) {
  try {
    if (!$rabbitmq->isConnected()) {
      echo "Reconnecting to RabbitMQ...\n";
      $rabbitmq = new App\RabbitMQClient();
    }

    echo "Waiting for messages...\n";

    // Обрабатываем несколько очередей
    $rabbitmq->consume('user_created', $userCreatedHandler, [
      'consumer_tag' => 'user_worker',
      'prefetch_count' => 1
    ]);

    $rabbitmq->consume('background_tasks', $backgroundTaskHandler, [
      'consumer_tag' => 'task_worker',
      'prefetch_count' => 1
    ]);

  } catch (Exception $e) {
    echo "Worker error: " . $e->getMessage() . "\n";
    echo "Restarting in 5 seconds...\n";
    sleep(5);
  }
}

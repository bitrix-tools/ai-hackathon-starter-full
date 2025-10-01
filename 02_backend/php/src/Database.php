<?php

class Database {
  private $connection;

  public function __construct() {
    $host = getenv('DB_HOST') ?: 'database';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'appdb';
    $username = getenv('DB_USER') ?: 'appuser';
    $password = getenv('DB_PASSWORD') ?: 'apppass';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";

    try {
      $this->connection = new PDO($dsn);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
      exit;
    }
  }

  public function getConnection() {
    return $this->connection;
  }

  public function query($sql, $params = []) {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      throw new Exception('Database query failed: ' . $e->getMessage());
    }
  }
}

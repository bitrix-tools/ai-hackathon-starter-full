<?php

namespace App;

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
      $this->connection = new \PDO($dsn);
      $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      throw new \Exception('Database connection failed: ' . $e->getMessage());
    }
  }

  public function query($sql, $params = []) {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}

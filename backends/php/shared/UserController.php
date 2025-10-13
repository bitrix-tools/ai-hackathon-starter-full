<?php

namespace App;

class UserController
{
  private $db;

  public function __construct($database)
  {
    $this->db = $database;
  }

  public function getUsers()
  {
    $stmt = $this->db->query("SELECT * FROM users ORDER BY id");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * @throws \Exception
   */
  public function createUser($data)
  {
    if (!$data || !isset($data['name']) || !isset($data['email'])) {
      throw new \Exception('Name and email are required', 400);
    }

    $name = trim($data['name']);
    $email = trim($data['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new \Exception('Invalid email format', 400);
    }

    try {
      $stmt = $this->db->query(
        "INSERT INTO users (name, email) VALUES (?, ?) RETURNING *",
        [$name, $email]
      );

      $newUser = $stmt->fetch(\PDO::FETCH_ASSOC);

      return $newUser;
    } catch (\Exception $e) {
      // Test is error about unique
      if (strpos($e->getMessage(), 'unique') !== false) {
        throw new \Exception('Email already exists', 400);
      }

      throw new \Exception($e->getMessage(), $e->getCode());
    }
  }
}

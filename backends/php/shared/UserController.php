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
        try {
            $stmt = $this->db->query("SELECT * FROM users ORDER BY id");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode($users);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function createUser($data)
    {
        if (!$data || !isset($data['name']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and email are required']);
            return;
        }

        $name = trim($data['name']);
        $email = trim($data['email']);

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email format']);
            return;
        }

        try {
            $stmt = $this->db->query(
                "INSERT INTO users (name, email) VALUES (?, ?) RETURNING *",
                [$name, $email]
            );

            $newUser = $stmt->fetch(\PDO::FETCH_ASSOC);

            http_response_code(201);
            echo json_encode($newUser);
        } catch (\Exception $e) {
            // Проверяем, является ли ошибка нарушением уникальности
            if (strpos($e->getMessage(), 'unique') !== false) {
                http_response_code(400);
                echo json_encode(['error' => 'Email already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }
}

<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_URI'] === '/api/health') {
  echo json_encode(["status" => "healthy", "backend" => "php"]);
} else {
  echo json_encode(["message" => "PHP Backend is running"]);
}

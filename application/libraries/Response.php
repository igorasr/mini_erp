<?php 

class Response
{
  public static function json(array $data, int $statusCode = 200): void
  {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
  }
}
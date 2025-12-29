<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

function read_json_body(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function now_iso(): string {
  $dt = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
  return $dt->format(DateTime::ATOM);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([
    'ok' => 0,
    'message' => 'Method Not Allowed. Use POST.',
    'server_time' => now_iso(),
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$body = read_json_body();
$user = trim((string)($_POST['user'] ?? $body['user'] ?? ''));
$pass = trim((string)($_POST['pass'] ?? $body['pass'] ?? ''));

$expected_user = 'sanpham';
$expected_pass = 'sp@123';

if ($user === '' || $pass === '') {
  http_response_code(400);
  echo json_encode([
    'ok' => 0,
    'message' => 'Thiếu tham số user hoặc pass',
    'server_time' => now_iso(),
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($user !== $expected_user || $pass !== $expected_pass) {
  http_response_code(401);
  echo json_encode([
    'ok' => 0,
    'message' => 'Đăng nhập thất bại',
    'server_time' => now_iso(),
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode([
  'ok' => 1,
  'message' => 'Đăng nhập thành công',
  'user' => $user,
  'server_time' => now_iso(),
], JSON_UNESCAPED_UNICODE);

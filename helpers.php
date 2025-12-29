<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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

function map_row(array $r): array {
  return [
    'id' => (int)$r['ma_san_pham'],
    'name' => (string)$r['ten_san_pham'],
    'price' => (float)$r['gia'],
    'stock' => (int)$r['ton_kho'],
    'on_sale' => ((int)$r['con_ban'] === 1),
  ];
}

function out_ok(string $action, array $items, array $extra = []): void {
  $totalValue = 0.0;
  foreach ($items as $it) {
    if (isset($it['price'], $it['stock'])) $totalValue += ((float)$it['price'] * (int)$it['stock']);
  }
  echo json_encode(array_merge([
    'ok' => 1,
    'entity' => 'san_pham',
    'action' => $action,
    'stats' => [
      'count' => count($items),
      'total_inventory_value' => $totalValue,
      'server_time' => now_iso(),
    ],
    'items' => $items,
  ], $extra), JSON_UNESCAPED_UNICODE);
  exit;
}

function out_err(string $message, array $fields = []): void {
  http_response_code(400);
  echo json_encode([
    'ok' => 0,
    'entity' => 'san_pham',
    'error' => [
      'message' => $message,
      'fields' => $fields,
      'server_time' => now_iso(),
    ]
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

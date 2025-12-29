<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/sanpham_dal.php';

$dal = new SanPhamDAL();
$body = read_json_body();
$req = array_merge($_POST, $body);
$action = strtolower((string)($req['action'] ?? 'getall'));

try {
  if ($action === 'getall') {
    $items = array_map('map_row', $dal->getAll());
    out_ok('getall', $items);
  }

  if ($action === 'get') {
    $id = (int)($req['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $row = $dal->getById($id);
    if (!$row) out_err('not found');
    out_ok('get', [map_row($row)]);
  }

  if ($action === 'insert') {
    $name = trim((string)($req['ten_san_pham'] ?? $req['TEN_SAN_PHAM'] ?? ''));
    $price = (float)($req['gia'] ?? $req['GIA'] ?? 0);
    if ($name === '' || $price <= 0) out_err('validation failed', ['ten_san_pham','gia']);
    $id = $dal->insert($req);
    out_ok('insert', [], ['created_id' => $id]);
  }

  if ($action === 'update') {
    $id = (int)($req['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $ok = $dal->update($id, $req);
    out_ok('update', [], ['updated' => (bool)$ok]);
  }

  if ($action === 'delete') {
    $id = (int)($req['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $ok = $dal->delete($id);
    out_ok('delete', [], ['deleted' => (bool)$ok]);
  }

  if ($action === 'insertsome') {
    $items = $req['data'] ?? null;
    if (is_string($items)) $items = json_decode($items, true);
    if (!is_array($items)) out_err('data must be JSON array', ['data']);
    $n = $dal->insertSome($items);
    out_ok('insertsome', [], ['bulk_created' => $n]);
  }

  out_err('unknown action');
} catch (Throwable $e) {
  out_err('server error: ' . $e->getMessage());
}

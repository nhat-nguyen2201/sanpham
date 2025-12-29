<?php
declare(strict_types=1);
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/sanpham_dal.php';

$dal = new SanPhamDAL();
$action = strtolower((string)($_REQUEST['action'] ?? 'getall'));

try {
  if ($action === 'getall') {
    $items = array_map('map_row', $dal->getAll());
    out_ok('getall', $items);
  }

  if ($action === 'get') {
    $id = (int)($_REQUEST['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $row = $dal->getById($id);
    if (!$row) out_err('not found');
    out_ok('get', [map_row($row)]);
  }

  if ($action === 'insert') {
    $name = trim((string)($_REQUEST['ten_san_pham'] ?? $_REQUEST['TEN_SAN_PHAM'] ?? ''));
    $price = (float)($_REQUEST['gia'] ?? $_REQUEST['GIA'] ?? 0);
    if ($name === '' || $price <= 0) out_err('validation failed', ['ten_san_pham','gia']);
    $id = $dal->insert($_REQUEST);
    out_ok('insert', [], ['created_id' => $id]);
  }

  if ($action === 'update') {
    $id = (int)($_REQUEST['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $ok = $dal->update($id, $_REQUEST);
    out_ok('update', [], ['updated' => (bool)$ok]);
  }

  if ($action === 'delete') {
    $id = (int)($_REQUEST['ma_san_pham'] ?? 0);
    if ($id <= 0) out_err('ma_san_pham invalid', ['ma_san_pham']);
    $ok = $dal->delete($id);
    out_ok('delete', [], ['deleted' => (bool)$ok]);
  }

  if ($action === 'insertsome') {
    $raw = (string)($_REQUEST['data'] ?? '');
    $items = json_decode($raw, true);
    if (!is_array($items)) out_err('data must be JSON array', ['data']);
    $n = $dal->insertSome($items);
    out_ok('insertsome', [], ['bulk_created' => $n]);
  }

  out_err('unknown action');
} catch (Throwable $e) {
  out_err('server error: ' . $e->getMessage());
}

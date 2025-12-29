<?php
declare(strict_types=1);
require_once __DIR__ . '/dbconnection.php';

class SanPhamDAL {
  private string $table = 'san_pham';

  public function getAll(): array {
    $stmt = db()->query("SELECT ma_san_pham, ten_san_pham, gia, ton_kho, con_ban FROM {$this->table} ORDER BY ma_san_pham DESC");
    return $stmt->fetchAll();
  }

  public function getById(int $id): ?array {
    $stmt = db()->prepare("SELECT ma_san_pham, ten_san_pham, gia, ton_kho, con_ban FROM {$this->table} WHERE ma_san_pham = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  private function normalize(array $in): array {
    $ten_san_pham = trim((string)($in['TEN_SAN_PHAM'] ?? $in['ten_san_pham'] ?? ''));

    $gia = (float)($in['GIA'] ?? $in['gia'] ?? 0);
    if ($gia < 0) $gia = 0;
    $ton_kho = (int)($in['TON_KHO'] ?? $in['ton_kho'] ?? 0);
    if ($ton_kho < 0) $ton_kho = 0;
    $con_ban = (string)($in['CON_BAN'] ?? $in['con_ban'] ?? 'TRUE');
    $con_ban = (strtoupper((string)$con_ban) === 'TRUE' || (string)$con_ban === '1') ? 1 : 0;
    return [
      ':ten_san_pham' => $ten_san_pham,
      ':gia' => $gia,
      ':ton_kho' => $ton_kho,
      ':con_ban' => $con_ban,
    ];
  }

  public function insert(array $in): int {
    $data = $this->normalize($in);
    $stmt = db()->prepare("INSERT INTO {$this->table}(ten_san_pham, gia, ton_kho, con_ban) VALUES(:ten_san_pham, :gia, :ton_kho, :con_ban)");
    $stmt->execute($data);
    return (int)db()->lastInsertId();
  }

  public function update(int $id, array $in): bool {
    $data = $this->normalize($in);
    $data[':id'] = $id;
    $stmt = db()->prepare("UPDATE {$this->table} SET ten_san_pham=:ten_san_pham, gia=:gia, ton_kho=:ton_kho, con_ban=:con_ban WHERE ma_san_pham = :id");
    return $stmt->execute($data);
  }

  public function delete(int $id): bool {
    $stmt = db()->prepare("DELETE FROM {$this->table} WHERE ma_san_pham = :id");
    return $stmt->execute([':id' => $id]);
  }

  public function insertSome(array $items): int {
    $count = 0;
    db()->beginTransaction();
    try {
      foreach ($items as $it) {
        if (!is_array($it)) continue;
        $this->insert($it);
        $count++;
      }
      db()->commit();
    } catch (Throwable $e) {
      db()->rollBack();
      throw $e;
    }
    return $count;
  }
}

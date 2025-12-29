-- ĐỀ 6: Sản phẩm (JSON có stats tổng giá trị tồn kho)
CREATE DATABASE IF NOT EXISTS db_sanpham CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sanpham;

DROP TABLE IF EXISTS san_pham;
CREATE TABLE san_pham (
  ma_san_pham INT AUTO_INCREMENT PRIMARY KEY,
  ten_san_pham VARCHAR(255) NOT NULL,
  gia DECIMAL(12,2) NOT NULL DEFAULT 0,
  ton_kho INT NOT NULL DEFAULT 0,
  con_ban TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO san_pham(ten_san_pham, gia, ton_kho, con_ban) VALUES
('Giáo trình PHP', 120000, 25, 1),
('Giáo trình MySQL', 95000, 15, 1),
('Bộ đề luyện thi', 50000, 0, 0);

CREATE DATABASE IF NOT EXISTS gudang
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gudang;

DROP TABLE IF EXISTS barang;

CREATE TABLE barang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    jumlah INT UNSIGNED NOT NULL DEFAULT 0,
    harga_per_pcs DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO barang
(nama_barang, jumlah, harga_per_pcs)
VALUES
('Laptop', 10, 7500000),
('Mouse Wireless', 25, 150000),
('Keyboard', 15, 300000),
('Monitor', 8, 1800000),
('Kabel HDMI', 30, 75000);
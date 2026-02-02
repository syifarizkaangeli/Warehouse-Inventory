CREATE DATABASE gudang;
USE gudang;

CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    jumlah INT NOT NULL,
    harga_per_pcs INT NOT NULL
);
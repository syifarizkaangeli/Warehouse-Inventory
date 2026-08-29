DROP TABLE IF EXISTS barang;

CREATE TABLE barang (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nama_barang TEXT NOT NULL,
    jumlah INTEGER NOT NULL DEFAULT 0,
    harga_per_pcs INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO barang
(
    nama_barang,
    jumlah,
    harga_per_pcs
)
VALUES
(
    'Laptop',
    10,
    7500000
),
(
    'Keyboard',
    20,
    250000
),
(
    'Mouse',
    30,
    150000
);
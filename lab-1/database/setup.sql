CREATE DATABASE IF NOT EXISTS pasarkita_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pasarkita_lab;

CREATE TABLE IF NOT EXISTS pelanggan (
    id INT NOT NULL PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telepon VARCHAR(50) NOT NULL,
    alamat VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dataset fiktif. Jika aplikasi pertama kali dibuka, PHP juga dapat mengimpor CSV otomatis.
INSERT IGNORE INTO pelanggan (id, nama, email, telepon, alamat) VALUES
(1, 'Andi Saputra', 'andi@example.test', '081200000101', 'Jakarta'),
(2, 'Bunga Lestari', 'bunga@example.test', '081200000102', 'Bandung'),
(3, 'Citra Maharani', 'citra@example.test', '081200000103', 'Surabaya'),
(4, 'Dimas Pratama', 'dimas@example.test', '081200000104', 'Denpasar'),
(5, 'Eka Putri', 'eka@example.test', '081200000105', 'Yogyakarta'),
(6, 'Fajar Nugroho', 'fajar@example.test', '081200000106', 'Semarang'),
(7, 'Gita Permata', 'gita@example.test', '081200000107', 'Malang'),
(8, 'Hanif Ramadhan', 'hanif@example.test', '081200000108', 'Bogor');

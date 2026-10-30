CREATE DATABASE IF NOT EXISTS uts_perpustakaan
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uts_perpustakaan;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS peminjaman;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS anggota;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE anggota (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_anggota VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telepon VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE buku (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(150) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    tahun_terbit YEAR NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE peminjaman (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT UNSIGNED NOT NULL,
    buku_id INT UNSIGNED NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NULL,
    status ENUM('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_peminjaman_anggota FOREIGN KEY (anggota_id)
        REFERENCES anggota(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_peminjaman_buku FOREIGN KEY (buku_id)
        REFERENCES buku(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO anggota (no_anggota, nama, email, telepon) VALUES
('AGT-2026-001', 'Andi Pratama', 'andi.pratama@example.com', '081234560001'),
('AGT-2026-002', 'Siti Maharani', 'siti.maharani@example.com', '081234560002'),
('AGT-2026-003', 'Budi Santoso', 'budi.santoso@example.com', '081234560003'),
('AGT-2026-004', 'Nadia Putri', 'nadia.putri@example.com', '081234560004'),
('AGT-2026-005', 'Rizky Saputra', 'rizky.saputra@example.com', '081234560005');

INSERT INTO buku (kode_buku, judul, pengarang, tahun_terbit, stok) VALUES
('BK-001', 'Laskar Pelangi', 'Andrea Hirata', 2005, 8),
('BK-002', 'Bumi', 'Tere Liye', 2014, 6),
('BK-003', 'Filosofi Teras', 'Henry Manampiring', 2018, 5),
('BK-004', 'Laut Bercerita', 'Leila S. Chudori', 2017, 4),
('BK-005', 'Negeri 5 Menara', 'Ahmad Fuadi', 2009, 7);

INSERT INTO peminjaman (anggota_id, buku_id, tanggal_pinjam, tanggal_kembali, status) VALUES
(1, 1, '2026-10-01', '2026-10-08', 'dikembalikan'),
(2, 2, '2026-10-10', NULL, 'dipinjam'),
(3, 3, '2026-10-12', NULL, 'dipinjam'),
(4, 4, '2026-10-15', '2026-10-22', 'dikembalikan'),
(5, 5, '2026-10-18', NULL, 'dipinjam');

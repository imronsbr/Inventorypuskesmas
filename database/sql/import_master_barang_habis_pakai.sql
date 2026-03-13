-- SQL Script untuk Import Data Master Barang Habis Pakai dari CSV
-- Pastikan file CSV berada di lokasi yang dapat diakses oleh MySQL

-- 1. Buat tabel temporary untuk import data
CREATE TEMPORARY TABLE temp_master_barang_habis_pakai (
    kode_barang VARCHAR(255),
    nama_barang VARCHAR(255),
    spesifikasi TEXT,
    satuan VARCHAR(100),
    kategori VARCHAR(100),
    harga_satuan DECIMAL(15,2),
    keterangan TEXT
);

-- 2. Load data dari CSV ke tabel temporary
-- Sesuaikan path file CSV dengan lokasi file Anda
LOAD DATA INFILE '/path/to/master_barang_habis_pakai_sample.csv'
INTO TABLE temp_master_barang_habis_pakai
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(kode_barang, nama_barang, spesifikasi, satuan, kategori, harga_satuan, keterangan);

-- 3. Insert data dari temporary table ke tabel utama
-- Menggunakan INSERT ... ON DUPLICATE KEY UPDATE untuk handle duplicate kode_barang
INSERT INTO master_barang_habis_pakais 
(kode_barang, nama_barang, spesifikasi, satuan, kategori, harga_satuan, keterangan, created_at, updated_at)
SELECT 
    kode_barang,
    nama_barang,
    spesifikasi,
    satuan,
    kategori,
    COALESCE(harga_satuan, 0),
    keterangan,
    NOW(),
    NOW()
FROM temp_master_barang_habis_pakai
ON DUPLICATE KEY UPDATE
    nama_barang = VALUES(nama_barang),
    spesifikasi = VALUES(spesifikasi),
    satuan = VALUES(satuan),
    kategori = VALUES(kategori),
    harga_satuan = VALUES(harga_satuan),
    keterangan = VALUES(keterangan),
    updated_at = NOW();

-- 4. Hapus tabel temporary
DROP TEMPORARY TABLE temp_master_barang_habis_pakai;

-- 5. Verifikasi data yang berhasil diimport
SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT kode_barang) as unique_codes,
    MIN(created_at) as earliest_import,
    MAX(updated_at) as latest_update
FROM master_barang_habis_pakais;

-- 6. Tampilkan sample data yang berhasil diimport
SELECT 
    kode_barang,
    nama_barang,
    satuan,
    kategori,
    harga_satuan,
    created_at
FROM master_barang_habis_pakais 
ORDER BY created_at DESC 
LIMIT 10; 
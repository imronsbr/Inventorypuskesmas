# Import Data Master Barang Habis Pakai dari CSV

Dokumen ini menjelaskan cara mengimport data Master Barang Habis Pakai dari file CSV ke database.

## 📁 File yang Tersedia

### 1. File CSV
- `public/master_barang_habis_pakai_sample.csv` - Data sample dengan 50 item barang
- `public/master_barang_habis_pakai_template.csv` - Template kosong untuk diisi

### 2. Script Import
- `database/sql/import_master_barang_habis_pakai.sql` - Script SQL untuk import langsung
- `database/scripts/import_csv_to_master_barang_habis_pakai.php` - Script PHP untuk import programmatik

## 📋 Struktur Data CSV

File CSV harus memiliki header dan kolom sebagai berikut:

```csv
kode_barang,nama_barang,spesifikasi,satuan,kategori,harga_satuan,keterangan
```

### Penjelasan Kolom:

| Kolom | Tipe | Wajib | Deskripsi |
|-------|------|-------|-----------|
| `kode_barang` | String | ✅ | Kode unik barang (contoh: ATK-001) |
| `nama_barang` | String | ✅ | Nama barang |
| `spesifikasi` | Text | ❌ | Spesifikasi detail barang |
| `satuan` | String | ✅ | Satuan barang (Pcs, Rim, Botol, dll) |
| `kategori` | String | ✅ | Kategori barang (ATK, ART, Obat, dll) |
| `harga_satuan` | Decimal | ❌ | Harga per satuan (default: 0) |
| `keterangan` | Text | ❌ | Keterangan tambahan |

## 🚀 Cara Import

### Metode 1: Menggunakan Script PHP (Recommended)

1. **Siapkan file CSV**
   ```bash
   # Copy file CSV ke folder scripts
   cp public/master_barang_habis_pakai_sample.csv database/scripts/
   ```

2. **Jalankan script PHP**
   ```bash
   cd database/scripts
   php import_csv_to_master_barang_habis_pakai.php
   ```

3. **Hasil yang diharapkan**
   ```
   Memulai import data dari file: master_barang_habis_pakai_sample.csv
   ==========================================
   Imported: ATK-001 - Kertas A4 80gsm
   Imported: ATK-002 - Pulpen Biru
   ...
   
   ==========================================
   Hasil Import:
   - Data baru: 50
   - Data diupdate: 0
   - Error: 0
   - Total diproses: 50
   - Total data di database: 50
   ```

### Metode 2: Menggunakan SQL Script

1. **Sesuaikan path file CSV di script SQL**
   ```sql
   -- Edit file: database/sql/import_master_barang_habis_pakai.sql
   -- Ganti path sesuai lokasi file CSV Anda
   LOAD DATA INFILE '/path/to/master_barang_habis_pakai_sample.csv'
   ```

2. **Jalankan script SQL**
   ```bash
   mysql -u username -p database_name < database/sql/import_master_barang_habis_pakai.sql
   ```

### Metode 3: Import Manual via MySQL

1. **Buat tabel temporary**
   ```sql
   CREATE TEMPORARY TABLE temp_import (
       kode_barang VARCHAR(255),
       nama_barang VARCHAR(255),
       spesifikasi TEXT,
       satuan VARCHAR(100),
       kategori VARCHAR(100),
       harga_satuan DECIMAL(15,2),
       keterangan TEXT
   );
   ```

2. **Load data dari CSV**
   ```sql
   LOAD DATA INFILE 'master_barang_habis_pakai_sample.csv'
   INTO TABLE temp_import
   FIELDS TERMINATED BY ','
   ENCLOSED BY '"'
   LINES TERMINATED BY '\n'
   IGNORE 1 LINES;
   ```

3. **Insert ke tabel utama**
   ```sql
   INSERT INTO master_barang_habis_pakais 
   (kode_barang, nama_barang, spesifikasi, satuan, kategori, harga_satuan, keterangan, created_at, updated_at)
   SELECT 
       kode_barang, nama_barang, spesifikasi, satuan, kategori, 
       COALESCE(harga_satuan, 0), keterangan, NOW(), NOW()
   FROM temp_import
   ON DUPLICATE KEY UPDATE
       nama_barang = VALUES(nama_barang),
       spesifikasi = VALUES(spesifikasi),
       satuan = VALUES(satuan),
       kategori = VALUES(kategori),
       harga_satuan = VALUES(harga_satuan),
       keterangan = VALUES(keterangan),
       updated_at = NOW();
   ```

## 📊 Data Sample yang Tersedia

File `master_barang_habis_pakai_sample.csv` berisi 50 item barang dengan kategori:

- **ATK (Alat Tulis Kantor)**: 10 item
- **ART (Alat Rumah Tangga)**: 10 item  
- **Alat Makan**: 10 item
- **Alat Laboratorium**: 10 item
- **Obat**: 10 item

### Contoh Data:
```csv
ATK-001,Kertas A4 80gsm,Kertas A4 putih 80gsm 500 lembar/rim,Rim,ATK,45000.00,Kertas untuk print dan fotokopi
ART-001,Sabun Cuci Piring,Sabun cuci piring lemon 800ml,Botol,ART,15000.00,Sabun untuk mencuci peralatan makan
ALL-001,Tabung Reaksi,Tabung reaksi kaca 10ml,Pcs,Alat Laboratorium,25000.00,Tabung untuk percobaan laboratorium
```

## ⚠️ Validasi Data

Script PHP akan melakukan validasi:

1. **Format CSV**: Header harus sesuai dengan yang diharapkan
2. **Data Wajib**: `kode_barang`, `nama_barang`, `satuan`, `kategori` tidak boleh kosong
3. **Tipe Data**: `harga_satuan` akan dikonversi ke decimal
4. **Duplikasi**: Jika `kode_barang` sudah ada, data akan diupdate

## 🔧 Troubleshooting

### Error: "File CSV tidak ditemukan"
- Pastikan file CSV berada di folder yang sama dengan script PHP
- Periksa nama file di variabel `$csvFile`

### Error: "Header CSV tidak sesuai"
- Pastikan header CSV sesuai: `kode_barang,nama_barang,spesifikasi,satuan,kategori,harga_satuan,keterangan`
- Periksa encoding file CSV (gunakan UTF-8)

### Error: "Data wajib tidak lengkap"
- Pastikan kolom wajib terisi: `kode_barang`, `nama_barang`, `satuan`, `kategori`
- Periksa format data di file CSV

### Error MySQL: "Access denied"
- Pastikan user MySQL memiliki permission untuk `LOAD DATA INFILE`
- Atau gunakan script PHP sebagai alternatif

## 📝 Tips

1. **Backup Database**: Selalu backup database sebelum import data besar
2. **Test dengan Data Kecil**: Test import dengan beberapa data dulu
3. **Validasi Data**: Periksa data CSV sebelum import
4. **Encoding**: Gunakan UTF-8 untuk file CSV
5. **Separator**: Gunakan koma (,) sebagai separator kolom

## 🎯 Contoh Penggunaan

### Membuat Data Sendiri

1. **Download template**
   ```bash
   cp public/master_barang_habis_pakai_template.csv my_data.csv
   ```

2. **Isi data sesuai format**
   ```csv
   kode_barang,nama_barang,spesifikasi,satuan,kategori,harga_satuan,keterangan
   BRG-001,Barang Contoh,Spesifikasi barang,Pcs,Kategori,10000.00,Keterangan barang
   ```

3. **Import menggunakan script PHP**
   ```bash
   # Edit nama file di script PHP
   $csvFile = 'my_data.csv';
   
   # Jalankan script
   php import_csv_to_master_barang_habis_pakai.php
   ```

## 📞 Support

Jika mengalami masalah, periksa:
1. Log error di console/terminal
2. Permission file dan folder
3. Konfigurasi database
4. Format file CSV 
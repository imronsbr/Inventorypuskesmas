# Panduan Upload CSV

## Cara Upload CSV Melalui Web Interface

### 1. Buka Aplikasi
```
http://localhost:8000
```

### 2. Login ke Aplikasi
Masukkan username/email dan password Anda

### 3. Navigasi ke Halaman Upload
- Klik menu **"Master Data"** di sidebar
- Pilih **"Barang Habis Pakai"**

### 4. Klik Tombol Import
- Di halaman Master Barang Habis Pakai, klik tombol **"Import"** (ikon upload)
- Modal upload akan muncul

### 5. Download Template (Opsional)
- Klik tombol **"Template"** untuk download format Excel yang benar
- Template akan berisi contoh data dan format yang sesuai

### 6. Upload File
- Klik **"Pilih File Excel"**
- Pilih file Excel (.xlsx atau .xls) yang sudah diisi data
- Klik **"Import Data"**

## Format Data yang Diperlukan

### Master Barang Habis Pakai:
| Kolom | Wajib | Contoh |
|-------|-------|--------|
| kode_barang | ✅ | HPK001 |
| nama_barang | ✅ | Kertas A4 |
| spesifikasi | ❌ | 80gsm |
| satuan | ✅ | Rim |
| kategori | ❌ | ATK |
| harga_satuan | ❌ | 50000 |
| keterangan | ❌ | Kertas untuk printer |

### Master Barang Modal:
| Kolom | Wajib | Contoh |
|-------|-------|--------|
| kode_barang | ✅ | MDL001 |
| nama_barang | ✅ | Meja Kerja |
| spesifikasi | ❌ | Kayu jati |
| satuan | ✅ | Unit |
| kategori | ❌ | Furniture |
| keterangan | ❌ | Meja kerja untuk staf |

### Master Obat:
| Kolom | Wajib | Contoh |
|-------|-------|--------|
| kode_obat | ✅ | OBT001 |
| nama_obat | ✅ | Paracetamol |
| jenis_obat | ❌ | Tablet |
| satuan | ✅ | Strip |
| keterangan | ❌ | Obat demam |
| stok | ❌ | 100 |

## Tips Upload yang Berhasil

1. **Pastikan format Excel benar** (.xlsx atau .xls)
2. **Ukuran file maksimal 2MB**
3. **Kolom wajib harus diisi** (lihat tabel di atas)
4. **Kode barang/obat harus unik** (tidak boleh duplikat)
5. **Gunakan template** yang disediakan untuk format yang benar

## File CSV Template yang Tersedia

Semua file template CSV tersedia di folder ini:
- `master_barang_habis_pakais.csv` - Template untuk barang habis pakai
- `master_barang_modals.csv` - Template untuk barang modal/asset
- `master_obats.csv` - Template untuk obat
- `users.csv` - Template untuk user
- `roles.csv` - Template untuk role
- `puskesmas.csv` - Template untuk puskesmas
- `ruangs.csv` - Template untuk ruang

## Cara Menggunakan Script PHP Manual

Jika ingin menggunakan script PHP:

```bash
cd database/scripts

# Import khusus master barang habis pakai
php import_csv_to_master_barang_habis_pakai.php

# Import generik untuk tabel apapun
php import_csv_to_table.php

# Batch import untuk semua tabel
php batch_import_csv.php
```

## Troubleshooting

### Error yang Sering Muncul:
1. **File terlalu besar**: Kompres atau kurangi data
2. **Format salah**: Pastikan file Excel (.xlsx/.xls)
3. **Data tidak lengkap**: Isi semua kolom wajib
4. **Kode duplikat**: Pastikan kode barang unik
5. **Server tidak berjalan**: Jalankan `php artisan serve`

### Jika Server Tidak Berjalan:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Kemudian buka: `http://localhost:8000` 
<?php

namespace App\Services;

use App\Models\PengadaanBarang;
use App\Models\StokHabisPakai;
use App\Models\MasterObat;
use App\Models\DetailBarangModal;
use App\Models\DetailPengadaanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenerimaanBarangService
{
    /**
     * Proses penerimaan barang dari pengadaan yang sudah completed.
     */
    public function processPengadaan(PengadaanBarang $pengadaan)
    {
        Log::info("Memulai proses penerimaan barang untuk Pengadaan ID: {$pengadaan->id} ({$pengadaan->nomor_pengadaan})");

        DB::beginTransaction();
        try {
            foreach ($pengadaan->detailPengadaanBarangs as $detail) {
                $this->processDetailItem($detail, $pengadaan);
            }

            DB::commit();
            Log::info("Berhasil memproses penerimaan barang untuk Pengadaan ID: {$pengadaan->id}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal memproses penerimaan barang: " . $e->getMessage());
            throw $e;
        }
    }

    private function processDetailItem(DetailPengadaanBarang $detail, PengadaanBarang $pengadaan)
    {
        switch ($detail->barang_type) {
            case 'habis_pakai': // String identifier used in Controller/Form
            case 'App\Models\MasterBarangHabisPakai': // Possible polymorphic type
                $this->processBarangHabisPakai($detail, $pengadaan);
                break;

            case 'modal':
            case 'App\Models\MasterBarangModal':
                $this->processBarangModal($detail, $pengadaan);
                break;

            case 'obat':
            case 'App\Models\MasterObat':
                $this->processObat($detail, $pengadaan);
                break;

            default:
                Log::warning("Tipe barang tidak dikenali atau belum didukung: {$detail->barang_type}");
                break;
        }
    }

    private function processBarangHabisPakai($detail, $pengadaan)
    {
        // 1. Ambil stok terakhir untuk perhitungan saldo
        // Asumsi: Menggunakan lokasi default atau perlu logic penentuan lokasi.
        // Untuk saat ini kita ambil lokasi gudang utama atau null jika belum ada gudang.
        // TODO: Idealnya ada kolom 'lokasi_id' di pengadaan atau detail pengadaan.
        // Kita pakai Ruang 'Gudang' (id 1) sebagai default sementara atau null.

        // Cari stok terakhir barang ini
        $lastStock = StokHabisPakai::where('master_barang_habis_pakai_id', $detail->barang_id)
            ->latest()
            ->first();

        $stokAwal = $lastStock ? $lastStock->stok_akhir : 0;
        $masuk = $detail->jumlah;
        $stokAkhir = $stokAwal + $masuk;

        // Create Kartu Stok Baru
        StokHabisPakai::create([
            'master_barang_habis_pakai_id' => $detail->barang_id,
            'periode' => now()->format('Y-m'),
            'stok_awal' => $stokAwal,
            'masuk' => $masuk,
            'keluar' => 0,
            'stok_akhir' => $stokAkhir,
            'harga_satuan' => $detail->harga_satuan,
            'nilai_akhir' => $stokAkhir * $detail->harga_satuan,
            'lokasi_id' => 1, // Default Gudang Utama, perlu disesuaikan dengan Master Ruang
            'keterangan' => "Penerimaan dari Pengadaan No: {$pengadaan->nomor_pengadaan}",
        ]);

        Log::info("Stok Habis Pakai ID {$detail->barang_id} bertambah {$masuk}.");
    }

    private function processBarangModal($detail, $pengadaan)
    {
        // Untuk barang modal, kita create row per unit (Asset Register)
        for ($i = 1; $i <= $detail->jumlah; $i++) {
            DetailBarangModal::create([
                'kode_barang' => \App\Models\MasterBarangModal::find($detail->barang_id)->kode_barang ?? 'UNKNOWN', // Perlu lookup kode master
                // 'nomor_seri' => null, // Nanti diisi manual saat labeling
                'merk' => $detail->spesifikasi, // Sementara mapping spesifikasi ke merk/tipe jika cocok
                // 'tipe' => ...,
                'tahun_perolehan' => now()->year,
                'ruang_id' => 1, // Default Gudang
                'kondisi' => DetailBarangModal::KONDISI_BAIK,
                'harga' => $detail->harga_satuan,
                'kapitalisasi' => 0,
                'keterangan' => "Pengadaan No: {$pengadaan->nomor_pengadaan}. " . $detail->keterangan,
            ]);
        }

        Log::info("Barang Modal ID {$detail->barang_id} didaftarkan sebanyak {$detail->jumlah} unit.");
    }

    private function processObat($detail, $pengadaan)
    {
        $obat = MasterObat::find($detail->barang_id);
        if ($obat) {
            $obat->stok = ($obat->stok ?? 0) + $detail->jumlah;
            $obat->save();
            Log::info("Stok Obat {$obat->nama_obat} bertambah {$detail->jumlah}.");
        }
    }
}

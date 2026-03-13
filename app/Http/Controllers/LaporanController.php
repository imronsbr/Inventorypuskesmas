<?php

namespace App\Http\Controllers;

use App\Models\StokHabisPakai;
use App\Models\MasterBarangHabisPakai;
use App\Models\MasterBarangModal;
use App\Models\MasterObat;
use App\Models\MonitoringAlkes;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function stok(Request $request)
    {
        $query = StokHabisPakai::with(['masterBarangHabisPakai', 'lokasi']);
        
        // Filter by periode
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        
        // Filter by lokasi
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        
        $stoks = $query->get();
        
        // Calculate summary statistics
        $summary = [
            'total_nilai' => $stoks->sum('nilai_akhir'),
            'total_barang' => $stoks->count(),
            'stok_habis' => $stoks->where('stok_akhir', '<=', 0)->count(),
            'stok_rendah' => $stoks->where('stok_akhir', '>', 0)->where('stok_akhir', '<=', 10)->count(),
        ];
        
        // Chart data for stock by category
        $chartData = $stoks->groupBy('masterBarangHabisPakai.kategori')
            ->map(function ($items) {
                return [
                    'total_nilai' => $items->sum('nilai_akhir'),
                    'total_barang' => $items->count(),
                ];
            });
        
        // Chart data for stock by location
        $locationChartData = $stoks->groupBy('lokasi.nama')
            ->map(function ($items) {
                return [
                    'total_nilai' => $items->sum('nilai_akhir'),
                    'total_barang' => $items->count(),
                ];
            });
        
        return Inertia::render('Barang/Laporan/Stok', [
            'stoks' => $stoks,
            'summary' => $summary,
            'chartData' => $chartData,
            'locationChartData' => $locationChartData,
            'filters' => [
                'periode' => $request->periode,
                'lokasi_id' => $request->lokasi_id,
            ],
        ]);
    }

    public function permintaan(Request $request)
    {
        $query = PermintaanBarang::with(['details.masterBarang', 'user', 'ruang', 'puskesmas']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }
        
        $permintaans = $query->get();
        
        // Calculate summary statistics
        $summary = [
            'total_permintaan' => $permintaans->count(),
            'diajukan' => $permintaans->where('status', 'diajukan')->count(),
            'disetujui' => $permintaans->where('status', 'disetujui')->count(),
            'ditolak' => $permintaans->where('status', 'ditolak')->count(),
        ];
        
        // Chart data for requests by status
        $statusChartData = $permintaans->groupBy('status')
            ->map(function ($items) {
                return $items->count();
            });
        
        // Chart data for requests by month
        $monthlyChartData = $permintaans->groupBy(function ($item) {
            return $item->created_at->format('Y-m');
        })->map(function ($items) {
            return $items->count();
        });
        
        return Inertia::render('Barang/Laporan/Permintaan', [
            'permintaans' => $permintaans,
            'summary' => $summary,
            'statusChartData' => $statusChartData,
            'monthlyChartData' => $monthlyChartData,
            'filters' => [
                'status' => $request->status,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
            ],
        ]);
    }

    public function exportStok(Request $request)
    {
        $query = StokHabisPakai::with(['masterBarangHabisPakai', 'lokasi']);
        
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        
        $stoks = $query->get();
        
        // Generate CSV
        $filename = 'laporan_stok_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stoks) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Kode Barang', 'Nama Barang', 'Kategori', 'Periode', 'Stok Awal', 
                'Masuk', 'Keluar', 'Stok Akhir', 'Harga Satuan', 
                'Nilai Akhir', 'Lokasi', 'Keterangan'
            ]);
            
            // Data
            foreach ($stoks as $stok) {
                fputcsv($file, [
                    $stok->masterBarangHabisPakai->kode_barang,
                    $stok->masterBarangHabisPakai->nama_barang,
                    $stok->masterBarangHabisPakai->kategori,
                    $stok->periode,
                    $stok->stok_awal,
                    $stok->masuk,
                    $stok->keluar,
                    $stok->stok_akhir,
                    $stok->harga_satuan,
                    $stok->nilai_akhir,
                    $stok->lokasi->nama,
                    $stok->keterangan,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportPermintaan(Request $request)
    {
        $query = PermintaanBarang::with(['details.masterBarang', 'user', 'ruang', 'puskesmas']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }
        
        $permintaans = $query->get();
        
        // Generate CSV
        $filename = 'laporan_permintaan_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($permintaans) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID Permintaan', 'Tanggal', 'Pemohon', 'Status', 'Ruang', 
                'Puskesmas', 'Barang', 'Jumlah', 'Catatan'
            ]);
            
            // Data
            foreach ($permintaans as $permintaan) {
                foreach ($permintaan->details as $detail) {
                    fputcsv($file, [
                        $permintaan->id,
                        $permintaan->created_at->format('Y-m-d'),
                        $permintaan->user->name,
                        $permintaan->status,
                        $permintaan->ruang->nama,
                        $permintaan->puskesmas->nama,
                        $detail->masterBarang->nama,
                        $detail->jumlah,
                        $permintaan->catatan,
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 
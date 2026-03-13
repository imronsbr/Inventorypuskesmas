<?php

namespace App\Http\Controllers;

use App\Models\StokHabisPakai;
use App\Models\MasterBarangHabisPakai;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StokHabisPakaiController extends Controller
{
    public function index(Request $request)
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
        
        // Search by barang name
        if ($request->filled('search')) {
            $query->whereHas('masterBarangHabisPakai', function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->get('sortBy', 'periode');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);
        
        // Ensure perPage is a valid integer
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage)); // Ensure it's between 1 and 100
        
        $stoks = $query->paginate($perPage)->withQueryString();
        
        // Get unique periods and locations for filters
        $periodes = StokHabisPakai::distinct()->pluck('periode');
        $lokasis = Ruang::select('id', 'nama')->orderBy('nama')->get();
        
        // Calculate summary statistics
        $summary = [
            'total_nilai' => StokHabisPakai::sum('nilai_akhir'),
            'total_barang' => StokHabisPakai::count(),
            'stok_habis' => StokHabisPakai::where('stok_akhir', '<=', 0)->count(),
        ];
        
        return Inertia::render('Barang/StokHabisPakai/Index', [
            'stoks' => $stoks,
            'filters' => [
                'periode' => $request->periode,
                'lokasi_id' => $request->lokasi_id,
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $request->get('perPage', 10),
            ],
            'periodes' => $periodes,
            'lokasis' => $lokasis,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:master_barang_habis_pakais,id',
            'periode' => 'required|string|max:20',
            'stok_awal' => 'required|numeric|min:0',
            'masuk' => 'required|numeric|min:0',
            'keluar' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'lokasi_id' => 'required|exists:ruangs,id',
            'keterangan' => 'nullable|string',
        ]);

        // Calculate stok_akhir and nilai_akhir
        $validated['stok_akhir'] = $validated['stok_awal'] + $validated['masuk'] - $validated['keluar'];
        $validated['nilai_akhir'] = $validated['stok_akhir'] * $validated['harga_satuan'];

        StokHabisPakai::create($validated);

        return redirect()->back()->with('success', 'Stok barang habis pakai berhasil ditambahkan!');
    }

    public function update(Request $request, StokHabisPakai $stokHabisPakai)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:master_barang_habis_pakais,id',
            'periode' => 'required|string|max:20',
            'stok_awal' => 'required|numeric|min:0',
            'masuk' => 'required|numeric|min:0',
            'keluar' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
            'lokasi_id' => 'required|exists:ruangs,id',
            'keterangan' => 'nullable|string',
        ]);

        // Calculate stok_akhir and nilai_akhir
        $validated['stok_akhir'] = $validated['stok_awal'] + $validated['masuk'] - $validated['keluar'];
        $validated['nilai_akhir'] = $validated['stok_akhir'] * $validated['harga_satuan'];

        $stokHabisPakai->update($validated);

        return redirect()->back()->with('success', 'Stok barang habis pakai berhasil diperbarui!');
    }

    public function destroy(StokHabisPakai $stokHabisPakai)
    {
        $stokHabisPakai->delete();

        return redirect()->back()->with('success', 'Stok barang habis pakai berhasil dihapus!');
    }

    public function export(Request $request)
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
        $filename = 'stok_habis_pakai_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stoks) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Kode Barang', 'Nama Barang', 'Periode', 'Stok Awal', 
                'Masuk', 'Keluar', 'Stok Akhir', 'Harga Satuan', 
                'Nilai Akhir', 'Lokasi', 'Keterangan'
            ]);
            
            // Data
            foreach ($stoks as $stok) {
                fputcsv($file, [
                    $stok->masterBarangHabisPakai->kode_barang,
                    $stok->masterBarangHabisPakai->nama_barang,
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
} 
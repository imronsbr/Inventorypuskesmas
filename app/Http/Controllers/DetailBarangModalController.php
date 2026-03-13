<?php

namespace App\Http\Controllers;

use App\Models\DetailBarangModal;
use App\Models\MasterBarangModal;
use App\Models\Ruang;
use App\Models\Puskesmas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DetailBarangModalExport;
use App\Exports\DetailBarangModalTemplateExport;
use App\Imports\DetailBarangModalImport;

class DetailBarangModalController extends Controller
{
    public function index(Request $request)
    {
        $query = DetailBarangModal::with([
            'masterBarangModal',
            'ruang.puskesmas',
            'monitoringAlkes'
        ]);

        // Filter by kode_barang
        if ($request->filled('kode_barang')) {
            $query->where('kode_barang', $request->kode_barang);
        }

        // Filter by ruang_id
        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        // Filter by kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Search by nomor_seri, merk, tipe
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_seri', 'like', '%' . $request->search . '%')
                  ->orWhere('merk', 'like', '%' . $request->search . '%')
                  ->orWhere('tipe', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage));

        $detailBarangModals = $query->paginate($perPage)->withQueryString();

        // Get data for filters
        $masterBarangModals = MasterBarangModal::orderBy('nama_barang')->get(['kode_barang', 'nama_barang']);
        $ruangs = Ruang::with('puskesmas')->orderBy('nama')->get(['id', 'nama', 'puskesmas_id']);
        $puskesmas = Puskesmas::orderBy('nama')->get(['id', 'nama']);

        // Configuration options
        $config = [
            'kondisiOptions' => [
                ['value' => DetailBarangModal::KONDISI_BAIK, 'label' => 'Baik'],
                ['value' => DetailBarangModal::KONDISI_PERBAIKAN, 'label' => 'Perbaikan'],
                ['value' => DetailBarangModal::KONDISI_RUSAK_BERAT, 'label' => 'Rusak Berat'],
            ],
            'perPageOptions' => [10, 25, 50, 100],
        ];

        return Inertia::render('Barang/DetailBarangModal/Index', [
            'detailBarangModals' => $detailBarangModals,
            'masterBarangModals' => $masterBarangModals,
            'ruangs' => $ruangs,
            'puskesmas' => $puskesmas,
            'filters' => [
                'kode_barang' => $request->kode_barang,
                'ruang_id' => $request->ruang_id,
                'kondisi' => $request->kondisi,
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $perPage,
            ],
            'config' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|exists:master_barang_modals,kode_barang',
            'nomor_seri' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'tahun_perolehan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'ruang_id' => 'nullable|exists:ruangs,id',
            'kondisi' => 'nullable|in:' . implode(',', [
                DetailBarangModal::KONDISI_BAIK,
                DetailBarangModal::KONDISI_PERBAIKAN,
                DetailBarangModal::KONDISI_RUSAK_BERAT
            ]),
            'nie' => 'nullable|string|max:255',
            'atribut' => 'nullable|array',
            'atribut.*' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'nullable|numeric|min:0',
            'kapitalisasi' => 'nullable|numeric|min:0',
            'jumlah' => 'nullable|integer|min:1',
        ]);

        DetailBarangModal::create($validated);

        return redirect()->back()->with('success', 'Detail barang modal berhasil ditambahkan!');
    }

    public function update(Request $request, DetailBarangModal $detailBarangModal)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|exists:master_barang_modals,kode_barang',
            'nomor_seri' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'tahun_perolehan' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'ruang_id' => 'nullable|exists:ruangs,id',
            'kondisi' => 'nullable|in:' . implode(',', [
                DetailBarangModal::KONDISI_BAIK,
                DetailBarangModal::KONDISI_PERBAIKAN,
                DetailBarangModal::KONDISI_RUSAK_BERAT
            ]),
            'nie' => 'nullable|string|max:255',
            'atribut' => 'nullable|array',
            'atribut.*' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'nullable|numeric|min:0',
            'kapitalisasi' => 'nullable|numeric|min:0',
            'jumlah' => 'nullable|integer|min:1',
        ]);

        $detailBarangModal->update($validated);

        return redirect()->back()->with('success', 'Detail barang modal berhasil diperbarui!');
    }

    public function destroy(DetailBarangModal $detailBarangModal)
    {
        $detailBarangModal->delete();
        return redirect()->back()->with('success', 'Detail barang modal berhasil dihapus!');
    }

    public function restore(DetailBarangModal $detailBarangModal)
    {
        $detailBarangModal->restore();
        return redirect()->back()->with('success', 'Detail barang modal berhasil direstore!');
    }

    public function downloadTemplate()
    {
        try {
            if (!class_exists('\App\Exports\DetailBarangModalTemplateExport')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Class DetailBarangModalTemplateExport not found'
                ], 500);
            }
            
            if (!class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Excel facade not found'
                ], 500);
            }
            
            $export = new DetailBarangModalTemplateExport();
            
            return Excel::download($export, 'template_detail_barang_modal.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            Excel::import(new DetailBarangModalImport(), $request->file('file'));
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimpor!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function export()
    {
        return Excel::download(new DetailBarangModalExport(), 'detail_barang_modal_' . date('Y-m-d') . '.xlsx');
    }
}

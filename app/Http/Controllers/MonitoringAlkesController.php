<?php

namespace App\Http\Controllers;

use App\Models\MonitoringAlkes;
use App\Models\DetailBarangModal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitoringAlkesController extends Controller
{
    public function index(Request $request)
    {
        $query = MonitoringAlkes::with(['detailBarangModal.masterBarangModal']);
        
        // Filter by kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        
        // Filter by tanggal
        if ($request->filled('tanggal_monitor')) {
            $query->whereDate('tanggal_monitor', $request->tanggal_monitor);
        }
        
        // Search by barang name
        if ($request->filled('search')) {
            $query->whereHas('detailBarangModal.masterBarangModal', function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->get('sortBy', 'tanggal_monitor');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);
        
        // Ensure perPage is a valid integer
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage)); // Ensure it's between 1 and 100
        
        $monitorings = $query->paginate($perPage)->withQueryString();
        
        // Get unique conditions for filter
        $kondisis = MonitoringAlkes::distinct()->pluck('kondisi');
        
        // Calculate summary statistics
        $summary = [
            'total_alkes' => MonitoringAlkes::count(),
            'baik' => MonitoringAlkes::where('kondisi', 'baik')->count(),
            'rusak_ringan' => MonitoringAlkes::where('kondisi', 'rusak ringan')->count(),
            'rusak_berat' => MonitoringAlkes::where('kondisi', 'rusak berat')->count(),
        ];
        
        return Inertia::render('Barang/MonitoringAlkes/Index', [
            'monitorings' => $monitorings,
            'filters' => [
                'kondisi' => $request->kondisi,
                'tanggal_monitor' => $request->tanggal_monitor,
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $request->get('perPage', 10),
            ],
            'kondisis' => $kondisis,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'detail_barang_modal_id' => 'required|exists:detail_barang_modals,id',
            'tanggal_monitor' => 'required|date',
            'kondisi' => 'required|string|in:baik,rusak ringan,rusak berat',
            'posisi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        MonitoringAlkes::create($validated);

        return redirect()->back()->with('success', 'Monitoring ALKES berhasil ditambahkan!');
    }

    public function update(Request $request, MonitoringAlkes $monitoringAlkes)
    {
        $validated = $request->validate([
            'detail_barang_modal_id' => 'required|exists:detail_barang_modals,id',
            'tanggal_monitor' => 'required|date',
            'kondisi' => 'required|string|in:baik,rusak ringan,rusak berat',
            'posisi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $monitoringAlkes->update($validated);

        return redirect()->back()->with('success', 'Monitoring ALKES berhasil diperbarui!');
    }

    public function destroy(MonitoringAlkes $monitoringAlkes)
    {
        $monitoringAlkes->delete();

        return redirect()->back()->with('success', 'Monitoring ALKES berhasil dihapus!');
    }
} 
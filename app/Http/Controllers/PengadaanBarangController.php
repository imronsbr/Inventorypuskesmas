<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\DetailPengadaanBarang;
use App\Models\MasterBarangHabisPakai;
use App\Models\MasterBarangModal;
use App\Models\MasterObat;
use App\Models\MonitoringAlkes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PengadaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = PengadaanBarang::with(['createdBy', 'detailPengadaanBarangs']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis barang
        if ($request->filled('jenis_barang')) {
            $query->where('jenis_barang', $request->jenis_barang);
        }

        // Search by nomor pengadaan
        if ($request->filled('search')) {
            $query->where('nomor_pengadaan', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $pengadaanBarangs = $query->paginate(10)->withQueryString();

        // Get barang options for form
        $barangHabisPakai = MasterBarangHabisPakai::whereNull('deleted_at')->get();
        $barangModal = MasterBarangModal::whereNull('deleted_at')->get();
        $obat = MasterObat::whereNull('deleted_at')->get();
        $alkes = MonitoringAlkes::whereNull('deleted_at')->get();

        return Inertia::render('Barang/PengadaanBarang/Index', [
            'pengadaanBarangs' => $pengadaanBarangs,
            'barangHabisPakai' => $barangHabisPakai,
            'barangModal' => $barangModal,
            'obat' => $obat,
            'alkes' => $alkes,
            'filters' => $request->only(['status', 'jenis_barang', 'search', 'sortBy', 'sortDir']),
        ]);
    }

    public function create()
    {
        // Get barang options for form
        $barangHabisPakai = MasterBarangHabisPakai::whereNull('deleted_at')->get();
        $barangModal = MasterBarangModal::whereNull('deleted_at')->get();
        $obat = MasterObat::whereNull('deleted_at')->get();
        $alkes = MonitoringAlkes::whereNull('deleted_at')->get();

        return Inertia::render('Barang/PengadaanBarang/Create', [
            'barangHabisPakai' => $barangHabisPakai,
            'barangModal' => $barangModal,
            'obat' => $obat,
            'alkes' => $alkes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_barang' => 'required|in:habis_pakai,modal,obat,alkes',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_type' => 'required|string',
            'items.*.barang_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.spesifikasi' => 'nullable|string',
            'items.*.keterangan' => 'nullable|string',
        ]);

        // Generate nomor pengadaan
        $nomorPengadaan = 'PGB-' . date('Ymd') . '-' . str_pad(PengadaanBarang::count() + 1, 4, '0', STR_PAD_LEFT);

        $pengadaanBarang = PengadaanBarang::create([
            'nomor_pengadaan' => $nomorPengadaan,
            'jenis_barang' => $validated['jenis_barang'],
            'keterangan' => $validated['keterangan'],
            'status' => PengadaanBarang::STATUS_DRAFT,
            'created_by' => Auth::id(),
        ]);

        // Create detail items
        foreach ($validated['items'] as $item) {
            DetailPengadaanBarang::create([
                'pengadaan_barang_id' => $pengadaanBarang->id,
                'barang_type' => $item['barang_type'],
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'spesifikasi' => $item['spesifikasi'],
                'keterangan' => $item['keterangan'],
            ]);
        }

        return redirect()->route('pengadaan-barang.index')->with('success', 'Pengadaan barang berhasil dibuat!');
    }

    public function show(PengadaanBarang $pengadaanBarang)
    {
        $pengadaanBarang->load([
            'createdBy',
            'detailPengadaanBarangs' => function ($query) {
                $query->with('barang');
            }
        ]);

        return Inertia::render('Barang/PengadaanBarang/Show', [
            'pengadaanBarang' => $pengadaanBarang,
        ]);
    }

    public function approve(Request $request, PengadaanBarang $pengadaanBarang)
    {
        // Check if current user can approve
        if (!$pengadaanBarang->canBeApprovedBy(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menyetujui pengadaan ini.');
        }

        $nextStatus = $pengadaanBarang->getNextStatus();

        // Custom logic: If the next status is 'approved_kepala_tata_usaha' (Final Approval),
        // effectively mark it as 'completed' or just let it be 'approved_kepala_tata_usaha' 
        // and then have a separate 'complete' action?
        // The implementation plan said: "Menambahkan method complete($id) atau memodifikasi method approve terakhir."

        // Let's modify logic: If next status is null (end of chain), check if we can complete it.
        if (!$nextStatus) {
            // If currently approved by KTU, maybe we can complete it?
            if ($pengadaanBarang->status === PengadaanBarang::STATUS_APPROVED_KEPALA_TATA_USAHA) {
                $nextStatus = PengadaanBarang::STATUS_COMPLETED;
            } else {
                return back()->with('error', 'Status approval tidak valid atau sudah selesai.');
            }
        }

        $pengadaanBarang->update([
            'status' => $nextStatus,
        ]);

        // If we just updated to completed, the Observer will kick in.

        return back()->with('success', 'Pengadaan barang berhasil disetujui!');
    }

    public function reject(Request $request, PengadaanBarang $pengadaanBarang)
    {
        $request->validate([
            'alasan_reject' => 'required|string',
        ]);

        $pengadaanBarang->update([
            'status' => PengadaanBarang::STATUS_REJECTED,
            'alasan_reject' => $request->alasan_reject,
        ]);

        return back()->with('success', 'Pengadaan barang berhasil ditolak!');
    }

    public function destroy(PengadaanBarang $pengadaanBarang)
    {
        if ($pengadaanBarang->status !== PengadaanBarang::STATUS_DRAFT) {
            return back()->with('error', 'Hanya pengadaan dengan status draft yang dapat dihapus.');
        }

        $pengadaanBarang->delete();
        return back()->with('success', 'Pengadaan barang berhasil dihapus!');
    }
}

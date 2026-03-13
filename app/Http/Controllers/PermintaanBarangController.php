<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PermintaanBarang;
use App\Models\MasterBarangHabisPakai;
use App\Models\DetailPermintaanBarang;
use App\Models\PermintaanBarangLog;

class PermintaanBarangController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            
            // Debug: Check if user exists
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $query = PermintaanBarang::with(['details.masterBarangHabisPakai', 'logs.user', 'ruang', 'puskesmas']);
            
            // Check if user is penanggung jawab barang habis pakai
            if ($user->role && $user->role->name === 'penanggung_jawab_barang_habis_pakai') {
                // Penanggung jawab barang habis pakai bisa melihat semua permintaan
            } else {
                // User biasa hanya melihat permintaan miliknya
                $query->where('user_id', $user->id);
            }
            
            $permintaans = $query->orderByDesc('created_at')->get();
            
            // Ambil hanya master barang habis pakai
            $barangHabisPakai = MasterBarangHabisPakai::orderBy('nama_barang')->get();
            
            $puskesmas = \App\Models\Puskesmas::select('id', 'nama')->orderBy('nama')->get();
            $ruangs = \App\Models\Ruang::select('id', 'nama', 'puskesmas_id')->orderBy('nama')->get();
            
            return Inertia::render('Barang/PermintaanBarang/Index', [
                'permintaans' => $permintaans,
                'barangHabisPakai' => $barangHabisPakai,
                'puskesmas' => $puskesmas,
                'ruangs' => $ruangs,
            ]);
        } catch (\Exception $e) {
            // Log the error and return a simple response for debugging
            \Log::error('PermintaanBarangController error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barangs' => 'required|array|min:1',
            'barangs.*.master_barang_habis_pakai_id' => 'required|exists:master_barang_habis_pakais,id',
            'barangs.*.jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
            'ruang_id' => 'required|exists:ruangs,id',
            'puskesmas_id' => 'required|exists:puskesmas,id',
        ]);

        // Validasi stok tiap barang habis pakai
        foreach ($validated['barangs'] as $item) {
            $masterBarang = MasterBarangHabisPakai::find($item['master_barang_habis_pakai_id']);
            
            if ($masterBarang && $masterBarang->stok !== null && $item['jumlah'] > $masterBarang->stok) {
                return redirect()->back()->with('error', 'Jumlah permintaan barang "' . $masterBarang->nama . '" melebihi stok tersedia!');
            }
        }

        // Simpan permintaan utama (header)
        $permintaan = PermintaanBarang::create([
            'catatan' => $validated['catatan'] ?? null,
            'user_id' => auth()->id(),
            'status' => 'diajukan',
            'tanggal_pesanan' => now(),
            'ruang_id' => $validated['ruang_id'],
            'puskesmas_id' => $validated['puskesmas_id'],
        ]);

        // Simpan detail barang habis pakai
        foreach ($validated['barangs'] as $item) {
            $detail = $permintaan->details()->create([
                'jumlah' => $item['jumlah'],
                'master_barang_habis_pakai_id' => $item['master_barang_habis_pakai_id'],
            ]);

            // Log pengajuan per item
            PermintaanBarangLog::create([
                'permintaan_barang_id' => $permintaan->id,
                'user_id' => auth()->id(),
                'status' => 'diajukan',
                'catatan' => $validated['catatan'] ?? null,
                'master_barang_habis_pakai_id' => $item['master_barang_habis_pakai_id'],
                'jumlah_diajukan' => $item['jumlah'],
            ]);
        }

        // Kirim notifikasi ke penanggung jawab barang habis pakai
        $penanggungJawab = \App\Models\User::whereHas('role', function($query) {
            $query->where('name', 'penanggung_jawab_barang_habis_pakai');
        })->first();
        if ($penanggungJawab) {
            $penanggungJawab->notify(new \App\Notifications\PermintaanBarangMasuk($permintaan));
        }

        return redirect()->back()->with('success', 'Permintaan barang habis pakai berhasil diajukan!');
    }

    public function update(Request $request, $id)
    {
        // Logika untuk memperbarui status akurasi permintaan barang
    }

    public function approve(Request $request, $id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);
        $user = auth()->user();
        
        // Check if user is penanggung jawab barang habis pakai
        if (!$user->role || $user->role->name !== 'penanggung_jawab_barang_habis_pakai') {
            return redirect()->back()->with('error', 'Hanya penanggung jawab barang habis pakai yang dapat menyetujui permintaan ini.');
        }
        
        $validated = $request->validate([
            'jumlah_diberikan' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        $permintaan->status = 'disetujui';
        $permintaan->penanggung_jawab_id = auth()->id();
        $permintaan->save();

        // Update details
        foreach ($permintaan->details as $detail) {
            $detail->jumlah_diberikan = $validated['jumlah_diberikan'];
            $detail->status = 'disetujui';
            $detail->catatan_approval = $validated['catatan'] ?? null;
            $detail->save();
            
            // Update stok barang habis pakai
            if ($detail->master_barang_habis_pakai_id) {
                $masterBarang = MasterBarangHabisPakai::find($detail->master_barang_habis_pakai_id);
                if ($masterBarang) {
                    // Get the latest stock record
                    $latestStock = $masterBarang->stokHabisPakais()->latest()->first();
                    if ($latestStock && $latestStock->stok_akhir >= $validated['jumlah_diberikan']) {
                        // Create new stock record with reduced quantity
                        $masterBarang->stokHabisPakais()->create([
                            'periode' => now()->format('Y-m'),
                            'stok_awal' => $latestStock->stok_akhir,
                            'masuk' => 0,
                            'keluar' => $validated['jumlah_diberikan'],
                            'stok_akhir' => $latestStock->stok_akhir - $validated['jumlah_diberikan'],
                            'harga_satuan' => $latestStock->harga_satuan,
                            'nilai_akhir' => ($latestStock->stok_akhir - $validated['jumlah_diberikan']) * $latestStock->harga_satuan,
                            'lokasi_id' => $latestStock->lokasi_id,
                            'keterangan' => 'Pengurangan stok dari permintaan barang'
                        ]);
                    }
                }
            }
        }

        // Create log
        PermintaanBarangLog::create([
            'permintaan_barang_id' => $permintaan->id,
            'user_id' => auth()->id(),
            'status' => 'disetujui',
            'catatan' => $validated['catatan'] ?? null,
            'jumlah_diberikan' => $validated['jumlah_diberikan'],
        ]);

        return redirect()->back()->with('success', 'Permintaan barang habis pakai disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);
        $user = auth()->user();
        
        // Check if user is penanggung jawab barang habis pakai
        if (!$user->role || $user->role->name !== 'penanggung_jawab_barang_habis_pakai') {
            return redirect()->back()->with('error', 'Hanya penanggung jawab barang habis pakai yang dapat menolak permintaan ini.');
        }
        
        $permintaan->status = 'ditolak';
        $permintaan->penanggung_jawab_id = auth()->id();
        $permintaan->save();

        // Update details
        foreach ($permintaan->details as $detail) {
            $detail->status = 'ditolak';
            $detail->catatan_approval = $request->input('catatan');
            $detail->save();
        }

        PermintaanBarangLog::create([
            'permintaan_barang_id' => $permintaan->id,
            'user_id' => auth()->id(),
            'status' => 'ditolak',
            'catatan' => $request->input('catatan'),
        ]);

        return redirect()->back()->with('success', 'Permintaan barang habis pakai ditolak!');
    }

    public function show($id)
    {
        $permintaan = PermintaanBarang::with(['details.masterBarangHabisPakai', 'logs.user', 'ruang', 'puskesmas', 'user'])->findOrFail($id);
        
        return Inertia::render('Barang/PermintaanBarang/Detail', [
            'permintaan' => $permintaan,
            'details' => $permintaan->details,
            'logs' => $permintaan->logs,
            'puskesmas' => $permintaan->puskesmas,
            'ruang' => $permintaan->ruang,
            'user' => $permintaan->user,
        ]);
    }

    public function approvalDetail(Request $request, $id)
    {
        $permintaan = PermintaanBarang::findOrFail($id);
        $user = auth()->user();
        
        // Check if user is penanggung jawab barang habis pakai
        if (!$user->role || $user->role->name !== 'penanggung_jawab_barang_habis_pakai') {
            return redirect()->back()->with('error', 'Hanya penanggung jawab barang habis pakai yang dapat melakukan approval.');
        }
        
        $validated = $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'required|exists:detail_permintaan_barangs,id',
            'details.*.jumlah_diberikan' => 'required|integer|min:0',
            'details.*.status' => 'required|in:disetujui,ditolak',
            'details.*.catatan_approval' => 'nullable|string',
        ]);

        foreach ($validated['details'] as $detailInput) {
            $detail = DetailPermintaanBarang::find($detailInput['id']);
            $detail->jumlah_diberikan = $detailInput['jumlah_diberikan'];
            $detail->status = $detailInput['status'];
            $detail->catatan_approval = $detailInput['catatan_approval'] ?? null;
            $detail->save();
            
            // Update stok if approved
            if ($detailInput['status'] === 'disetujui' && $detail->master_barang_habis_pakai_id) {
                $masterBarang = MasterBarangHabisPakai::find($detail->master_barang_habis_pakai_id);
                if ($masterBarang) {
                    // Get the latest stock record
                    $latestStock = $masterBarang->stokHabisPakais()->latest()->first();
                    if ($latestStock && $latestStock->stok_akhir >= $detailInput['jumlah_diberikan']) {
                        // Create new stock record with reduced quantity
                        $masterBarang->stokHabisPakais()->create([
                            'periode' => now()->format('Y-m'),
                            'stok_awal' => $latestStock->stok_akhir,
                            'masuk' => 0,
                            'keluar' => $detailInput['jumlah_diberikan'],
                            'stok_akhir' => $latestStock->stok_akhir - $detailInput['jumlah_diberikan'],
                            'harga_satuan' => $latestStock->harga_satuan,
                            'nilai_akhir' => ($latestStock->stok_akhir - $detailInput['jumlah_diberikan']) * $latestStock->harga_satuan,
                            'lokasi_id' => $latestStock->lokasi_id,
                            'keterangan' => 'Pengurangan stok dari permintaan barang'
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Approval detail permintaan barang habis pakai berhasil disimpan!');
    }
}

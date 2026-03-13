<?php

namespace App\Http\Controllers;

use App\Models\Sip;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $query = Sip::where('pegawai_id', $pegawai->id);

        // Filter by status (aktif/tidak aktif berdasarkan tanggal)
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('tanggal_berakhir', '>=', now());
            } elseif ($request->status === 'tidak_aktif') {
                $query->where('tanggal_berakhir', '<', now());
            }
        }

        // Search by nomor SIP
        if ($request->filled('search')) {
            $query->where('nomor_sip', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'tanggal_berakhir');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $sips = $query->paginate(10)->withQueryString();

        return Inertia::render('Sip/Index', [
            'sips' => $sips,
            'pegawai' => $pegawai,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
            ],
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan!');
        }

        return Inertia::render('Sip/Create', [
            'pegawai' => $pegawai,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $validated = $request->validate([
            'nomor_sip' => 'required|string|max:255|unique:sip,nomor_sip',
            'tempat_praktik' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_terbit',
            'file_sip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'pegawai_id' => $pegawai->id,
            'nomor_sip' => $validated['nomor_sip'],
            'tempat_praktik' => $validated['tempat_praktik'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ];

        // Handle file upload
        if ($request->hasFile('file_sip')) {
            $file = $request->file('file_sip');
            $fileName = 'sip_' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/sip', $fileName);
            $data['file_path'] = $path;
        }

        Sip::create($data);

        return redirect()->route('sip.index')->with('success', 'Data SIP berhasil ditambahkan!');
    }

    public function show(Sip $sip)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only view their own SIP
        if (!$pegawai || $sip->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        return Inertia::render('Sip/Show', [
            'sip' => $sip,
            'pegawai' => $pegawai,
        ]);
    }

    public function edit(Sip $sip)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only edit their own SIP
        if (!$pegawai || $sip->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        return Inertia::render('Sip/Edit', [
            'sip' => $sip,
            'pegawai' => $pegawai,
        ]);
    }

    public function update(Request $request, Sip $sip)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only update their own SIP
        if (!$pegawai || $sip->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nomor_sip' => 'required|string|max:255|unique:sip,nomor_sip,' . $sip->id,
            'tempat_praktik' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_terbit',
            'file_sip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nomor_sip' => $validated['nomor_sip'],
            'tempat_praktik' => $validated['tempat_praktik'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ];

        // Handle file upload
        if ($request->hasFile('file_sip')) {
            // Delete old file if exists
            if ($sip->file_path && Storage::exists($sip->file_path)) {
                Storage::delete($sip->file_path);
            }
            
            $file = $request->file('file_sip');
            $fileName = 'sip_' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/sip', $fileName);
            $data['file_path'] = $path;
        }

        $sip->update($data);

        return redirect()->route('sip.index')->with('success', 'Data SIP berhasil diperbarui!');
    }

    public function destroy(Sip $sip)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only delete their own SIP
        if (!$pegawai || $sip->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        // Delete file if exists
        if ($sip->file_path && Storage::exists($sip->file_path)) {
            Storage::delete($sip->file_path);
        }

        $sip->delete();

        return redirect()->route('sip.index')->with('success', 'Data SIP berhasil dihapus!');
    }

    public function download(Sip $sip)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only download their own SIP
        if (!$pegawai || $sip->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        if (!$sip->file_path || !Storage::exists($sip->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::download($sip->file_path);
    }
} 
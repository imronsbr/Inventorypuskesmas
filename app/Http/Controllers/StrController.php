<?php

namespace App\Http\Controllers;

use App\Models\Str;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StrController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $query = Str::where('pegawai_id', $pegawai->id);

        // Filter by status (aktif/tidak aktif berdasarkan tanggal)
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('tanggal_berakhir', '>=', now());
            } elseif ($request->status === 'tidak_aktif') {
                $query->where('tanggal_berakhir', '<', now());
            }
        }

        // Search by nomor STR
        if ($request->filled('search')) {
            $query->where('nomor_str', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'tanggal_berakhir');
        $sortDir = $request->get('sortDir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $strs = $query->paginate(10)->withQueryString();

        return Inertia::render('Str/Index', [
            'strs' => $strs,
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

        return Inertia::render('Str/Create', [
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
            'nomor_str' => 'required|string|max:255|unique:str,nomor_str',
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_terbit',
            'file_str' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'pegawai_id' => $pegawai->id,
            'nomor_str' => $validated['nomor_str'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ];

        // Handle file upload
        if ($request->hasFile('file_str')) {
            $file = $request->file('file_str');
            $fileName = 'str_' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/str', $fileName);
            $data['file_path'] = $path;
        }

        Str::create($data);

        return redirect()->route('str.index')->with('success', 'Data STR berhasil ditambahkan!');
    }

    public function show(Str $str)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only view their own STR
        if (!$pegawai || $str->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        return Inertia::render('Str/Show', [
            'str' => $str,
            'pegawai' => $pegawai,
        ]);
    }

    public function edit(Str $str)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only edit their own STR
        if (!$pegawai || $str->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        return Inertia::render('Str/Edit', [
            'str' => $str,
            'pegawai' => $pegawai,
        ]);
    }

    public function update(Request $request, Str $str)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only update their own STR
        if (!$pegawai || $str->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nomor_str' => 'required|string|max:255|unique:str,nomor_str,' . $str->id,
            'tanggal_terbit' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_terbit',
            'file_str' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nomor_str' => $validated['nomor_str'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ];

        // Handle file upload
        if ($request->hasFile('file_str')) {
            // Delete old file if exists
            if ($str->file_path && Storage::exists($str->file_path)) {
                Storage::delete($str->file_path);
            }
            
            $file = $request->file('file_str');
            $fileName = 'str_' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/str', $fileName);
            $data['file_path'] = $path;
        }

        $str->update($data);

        return redirect()->route('str.index')->with('success', 'Data STR berhasil diperbarui!');
    }

    public function destroy(Str $str)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only delete their own STR
        if (!$pegawai || $str->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        // Delete file if exists
        if ($str->file_path && Storage::exists($str->file_path)) {
            Storage::delete($str->file_path);
        }

        $str->delete();

        return redirect()->route('str.index')->with('success', 'Data STR berhasil dihapus!');
    }

    public function download(Str $str)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        // Ensure user can only download their own STR
        if (!$pegawai || $str->pegawai_id !== $pegawai->id) {
            abort(403);
        }

        if (!$str->file_path || !Storage::exists($str->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::download($str->file_path);
    }
} 
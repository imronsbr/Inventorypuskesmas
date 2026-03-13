<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Agama;
use App\Models\Pendidikan;
use App\Models\Keluarga;
use App\Models\Str;
use App\Models\Sip;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with([
            'user',
            'jabatans',
            'agama',
            'pendidikan',
            'keluargas',
            'strs',
            'sips'
        ]);

        // Filter by nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        // Filter by jabatan
        if ($request->filled('jabatan_id')) {
            $query->whereHas('jabatans', function($q) use ($request) {
                $q->where('jabatans.id', $request->jabatan_id);
            });
        }

        // Filter by status pegawai
        if ($request->filled('status_pegawai')) {
            $query->where('status_pegawai', $request->status_pegawai);
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'nama');
        $sortDir = $request->get('sortDir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage));

        $pegawais = $query->paginate($perPage)->withQueryString();

        // Get data for filters
        $jabatans = Jabatan::orderBy('nama')->get(['id', 'nama']);
        $agamas = Agama::orderBy('nama')->get(['id', 'nama']);
        $pendidikans = Pendidikan::orderBy('nama')->get(['id', 'nama']);

        // Configuration options
        $config = [
            'statusPegawaiOptions' => [
                ['value' => 'pns', 'label' => 'PNS'],
                ['value' => 'cpns', 'label' => 'CPNS'],
                ['value' => 'non_pns', 'label' => 'Non PNS'],
                ['value' => 'pjlp', 'label' => 'PJLP'],
            ],
            'jenisKontrakOptions' => [
                ['value' => 'tetap', 'label' => 'Tetap'],
                ['value' => 'kontrak', 'label' => 'Kontrak'],
            ],
            'perPageOptions' => [10, 25, 50, 100],
        ];

        return Inertia::render('Pegawai/Index', [
            'pegawais' => $pegawais,
            'jabatans' => $jabatans,
            'agamas' => $agamas,
            'pendidikans' => $pendidikans,
            'filters' => [
                'search' => $request->search,
                'jabatan_id' => $request->jabatan_id,
                'status_pegawai' => $request->status_pegawai,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $perPage,
            ],
            'config' => $config,
        ]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load([
            'user',
            'jabatans',
            'agama',
            'pendidikan',
            'keluargas',
            'strs',
            'sips'
        ]);

        return Inertia::render('Pegawai/Show', [
            'pegawai' => $pegawai,
        ]);
    }

    public function create()
    {
        $jabatans = Jabatan::orderBy('nama')->get(['id', 'nama']);
        $agamas = Agama::orderBy('nama')->get(['id', 'nama']);
        $pendidikans = Pendidikan::orderBy('nama')->get(['id', 'nama']);
        $users = User::whereDoesntHave('pegawai')->get(['id', 'name', 'email']);

        return Inertia::render('Pegawai/Create', [
            'jabatans' => $jabatans,
            'agamas' => $agamas,
            'pendidikans' => $pendidikans,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:pegawais,nip',
            'unit_kerja' => 'nullable|string|max:255',
            'agama_id' => 'nullable|exists:agamas,id',
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'status_pegawai' => 'required|in:pns,cpns,non_pns,pjlp',
            'jenis_kontrak' => 'nullable|in:tetap,kontrak',
            'nik' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:l,p',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'jabatan_ids' => 'nullable|array',
            'jabatan_ids.*' => 'exists:jabatans,id',
        ]);

        $pegawai = Pegawai::create($validated);

        // Sync jabatan
        if (isset($validated['jabatan_ids'])) {
            $pegawai->jabatans()->sync($validated['jabatan_ids']);
        }

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    public function edit(Pegawai $pegawai)
    {
        $pegawai->load(['jabatans', 'keluargas', 'strs', 'sips']);
        
        $jabatans = Jabatan::orderBy('nama')->get(['id', 'nama']);
        $agamas = Agama::orderBy('nama')->get(['id', 'nama']);
        $pendidikans = Pendidikan::orderBy('nama')->get(['id', 'nama']);
        $users = User::whereDoesntHave('pegawai')->orWhere('id', $pegawai->user_id)->get(['id', 'name', 'email']);

        return Inertia::render('Pegawai/Edit', [
            'pegawai' => $pegawai,
            'jabatans' => $jabatans,
            'agamas' => $agamas,
            'pendidikans' => $pendidikans,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:pegawais,nip,' . $pegawai->id,
            'unit_kerja' => 'nullable|string|max:255',
            'agama_id' => 'nullable|exists:agamas,id',
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'status_pegawai' => 'required|in:pns,cpns,non_pns,pjlp',
            'jenis_kontrak' => 'nullable|in:tetap,kontrak',
            'nik' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:l,p',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'jabatan_ids' => 'nullable|array',
            'jabatan_ids.*' => 'exists:jabatans,id',
        ]);

        $pegawai->update($validated);

        // Sync jabatan
        if (isset($validated['jabatan_ids'])) {
            $pegawai->jabatans()->sync($validated['jabatan_ids']);
        }

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus!');
    }

    public function updateInline(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255|unique:pegawais,nip,' . $pegawai->id,
            'unit_kerja' => 'nullable|string|max:255',
            'status_pegawai' => 'required|in:pns,cpns,non_pns,pjlp',
            'jenis_kontrak' => 'nullable|in:tetap,kontrak',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'jabatan_ids' => 'nullable|array',
            'jabatan_ids.*' => 'exists:jabatans,id',
        ]);

        $pegawai->update($validated);

        // Sync jabatan if provided
        if (isset($validated['jabatan_ids'])) {
            $pegawai->jabatans()->sync($validated['jabatan_ids']);
        }

        // Load the updated pegawai with relationships
        $pegawai->load(['jabatans', 'agama', 'pendidikan']);

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diperbarui!',
            'pegawai' => $pegawai
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan!');
        }

        $pegawai->load([
            'jabatans',
            'agama',
            'pendidikan',
            'keluargas',
            'strs',
            'sips'
        ]);

        return Inertia::render('Pegawai/Profile', [
            'pegawai' => $pegawai,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MasterObat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterObatController extends Controller
{
    public function index(Request $request)
    {
        // Determine if we need to include trashed records
        $includeTrashed = $request->filled('status') && $request->status === 'inactive';
        
        // Start with appropriate query
        if ($includeTrashed) {
            $query = MasterObat::withTrashed();
        } else {
            $query = MasterObat::query();
        }
        
        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        } else {
            // Default to show only active records
            $query->whereNull('deleted_at');
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by bentuk
        if ($request->filled('bentuk')) {
            $query->where('bentuk', $request->bentuk);
        }
        
        // Filter by status kondisi (stok)
        if ($request->filled('kondisi')) {
            if ($request->kondisi === 'tersedia') {
                $query->where('stok', '>', 10);
            } elseif ($request->kondisi === 'stok_menipis') {
                $query->where('stok', '<=', 10)->where('stok', '>', 0);
            } elseif ($request->kondisi === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }
        
        // Search by nama_obat, kode_obat, or komposisi
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_obat', 'like', '%' . $request->search . '%')
                  ->orWhere('komposisi', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->get('sortBy', 'nama_obat');
        $sortDir = $request->get('sortDir', 'asc');
        $query->orderBy($sortBy, $sortDir);
        
        // Ensure perPage is a valid integer
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage)); // Ensure it's between 1 and 100
        
        $obats = $query->paginate($perPage)->withQueryString();
        
        // Get unique categories and forms for filters (include trashed records for inactive filter)
        if ($includeTrashed) {
            $kategoris = MasterObat::withTrashed()->distinct()->pluck('kategori');
            $bentuks = MasterObat::withTrashed()->distinct()->pluck('bentuk');
        } else {
            $kategoris = MasterObat::distinct()->pluck('kategori');
            $bentuks = MasterObat::distinct()->pluck('bentuk');
        }
        
        return Inertia::render('Barang/MasterObat/Index', [
            'obats' => $obats,
            'filters' => [
                'status' => $request->status,
                'kategori' => $request->kategori,
                'bentuk' => $request->bentuk,
                'kondisi' => $request->kondisi,
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $request->get('perPage', 10),
            ],
            'kategoris' => $kategoris,
            'bentuks' => $bentuks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_obat' => 'required|string|unique:master_obats,kode_obat',
            'nama_obat' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'bentuk' => 'required|string|max:100',
            'komposisi' => 'nullable|string',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'stok' => 'nullable|integer|min:0',
        ]);

        MasterObat::create($validated);

        return redirect()->back()->with('success', 'Obat berhasil ditambahkan!');
    }

    public function update(Request $request, MasterObat $masterObat)
    {
        $validated = $request->validate([
            'kode_obat' => 'required|string|unique:master_obats,kode_obat,' . $masterObat->id,
            'nama_obat' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'bentuk' => 'required|string|max:100',
            'komposisi' => 'nullable|string',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'stok' => 'nullable|integer|min:0',
        ]);

        $masterObat->update($validated);

        return redirect()->back()->with('success', 'Obat berhasil diperbarui!');
    }

    public function destroy(MasterObat $masterObat)
    {
        $masterObat->delete();

        return redirect()->back()->with('success', 'Obat berhasil dihapus!');
    }

    public function restore($id)
    {
        $masterObat = MasterObat::withTrashed()->findOrFail($id);
        $masterObat->restore();

        return redirect()->back()->with('success', 'Obat berhasil dikembalikan!');
    }
} 
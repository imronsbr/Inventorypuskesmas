<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\BarangExport;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = \App\Models\Barang::all();
        return Inertia::render('Barang/Index', [
            'barangs' => $barangs,
        ]);
    }

    public function request()
    {
        // Halaman permintaan barang
        return Inertia::render('Barang/Request');
    }

    public function stock()
    {
        // Halaman stok barang
        $barangs = Barang::all();
        return Inertia::render('Barang/Stock', [
            'barangs' => $barangs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
        ]);
        Barang::create($validated);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil disimpan!');
    }

    public function exportExcel()
    {
        return Excel::download(new BarangExport, 'barang.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        \Maatwebsite\Excel\Facades\Excel::import(new BarangImport, $request->file('file'));
        return redirect()->route('barang.index')->with('success', 'Import barang dari Excel berhasil!');
    }
}

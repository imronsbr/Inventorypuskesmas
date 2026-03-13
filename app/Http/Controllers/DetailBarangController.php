<?php

namespace App\Http\Controllers;

use App\Models\DetailBarang;
use App\Models\MasterBarang;
use App\Models\LogPerubahan; // Import model LogPerubahan
use Illuminate\Http\Request;
use Inertia\Inertia;

class DetailBarangController extends Controller
{
    public function index(Request $request)
    {
        $masterBarangId = $request->input('master_barang_id');
        $perPage = $request->input('perPage', 10);
        $query = DetailBarang::with('masterBarang');
        if ($masterBarangId) {
            $query->where('master_barang_id', $masterBarangId);
        }
        $details = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $masterBarangs = MasterBarang::orderBy('nama')->get(['id', 'nama', 'kode']);
        return Inertia::render('Barang/Detail', [
            'details' => $details,
            'masterBarangs' => $masterBarangs,
            'filter_master_barang_id' => $masterBarangId,
            'perPage' => $perPage,
        ]);
    }

    public function update(Request $request, DetailBarang $detailBarang)
    {
        $originalData = $detailBarang->toArray();

        $detailBarang->update($request->all());

        $changes = array_diff_assoc($detailBarang->toArray(), $originalData);

        LogPerubahan::create([
            'detail_barang_id' => $detailBarang->id,
            'action' => 'update',
            'changes' => $changes,
        ]);

        return redirect()->back()->with('success', 'Detail barang updated successfully.');
    }

    public function destroy(DetailBarang $detailBarang)
    {
        LogPerubahan::create([
            'detail_barang_id' => $detailBarang->id,
            'action' => 'delete',
            'changes' => null,
        ]);

        $detailBarang->delete();

        return redirect()->back()->with('success', 'Detail barang deleted successfully.');
    }

    public function getLog(DetailBarang $detailBarang)
    {
        $logs = $detailBarang->logPerubahan()->orderBy('created_at', 'desc')->get();

        return response()->json($logs);
    }
}

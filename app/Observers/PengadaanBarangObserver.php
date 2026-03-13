<?php

namespace App\Observers;

use App\Models\PengadaanBarang;
use App\Services\PenerimaanBarangService;
use Illuminate\Support\Facades\Log;

class PengadaanBarangObserver
{
    protected $penerimaanService;

    public function __construct(PenerimaanBarangService $penerimaanService)
    {
        $this->penerimaanService = $penerimaanService;
    }

    /**
     * Handle the PengadaanBarang "updated" event.
     *
     * @param  \App\Models\PengadaanBarang  $pengadaanBarang
     * @return void
     */
    public function updated(PengadaanBarang $pengadaanBarang)
    {
        // Check if status changed to 'completed'
        if ($pengadaanBarang->isDirty('status') && $pengadaanBarang->status === 'completed') {
            Log::info("Pengadaan Barang {$pengadaanBarang->nomor_pengadaan} status changed to COMPLETED. Processing goods receipt...");

            try {
                $this->penerimaanService->processPengadaan($pengadaanBarang);
            } catch (\Exception $e) {
                // Log failed attempt but don't stop the update itself ? 
                // Alternatively, we could throw exception to rollback transaction if this runs inside one.
                Log::error("Failed to process goods receipt for {$pengadaanBarang->nomor_pengadaan}: " . $e->getMessage());
            }
        }
    }
}

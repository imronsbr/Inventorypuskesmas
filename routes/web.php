<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DetailBarangController;
use App\Http\Controllers\DetailBarangModalController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\MasterBarangModalController;
use App\Http\Controllers\MasterObatController;
use App\Http\Controllers\StokHabisPakaiController;
use App\Http\Controllers\MonitoringAlkesController;
use App\Http\Controllers\LaporanController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Role;
use App\Http\Controllers\MasterBarangHabisPakaiController;
use App\Http\Controllers\PengadaanBarangController;
use App\Http\Controllers\StrController;
use App\Http\Controllers\SipController;

Route::get('/api/roles', function () {
    return Role::select('id', 'name')->get();
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ekinerja', function () {
    return Inertia::render('Ekinerja/Index');
})->name('ekinerja');
Route::get('/ekinerja/laporan', function () {
    return Inertia::render('Ekinerja/Laporan');
})->name('ekinerja.laporan');
Route::get('/ekinerja/sasaran', function () {
    return Inertia::render('Ekinerja/Sasaran');
})->name('ekinerja.sasaran');
Route::get('/ekinerja/aktivitas', function () {
    return Inertia::render('Ekinerja/Aktivitas');
})->name('ekinerja.aktivitas');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pegawai routes
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
    Route::get('/pegawai/{pegawai}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::patch('/pegawai/{pegawai}/inline', [PegawaiController::class, 'updateInline'])->name('pegawai.update-inline');
    Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // STR routes (pegawai can manage their own STR)
    Route::resource('str', StrController::class);
    Route::get('/str/{str}/download', [StrController::class, 'download'])->name('str.download');

    // SIP routes (pegawai can manage their own SIP)
    Route::resource('sip', SipController::class);
    Route::get('/sip/{sip}/download', [SipController::class, 'download'])->name('sip.download');

    
    // Role management routes
    Route::resource('roles', RoleController::class);
    
    // Barang routes - Master Data
    Route::get('/master-barang-habis-pakai', [MasterBarangHabisPakaiController::class, 'index'])->name('master-barang-habis-pakai.index');
    Route::post('/master-barang-habis-pakai', [MasterBarangHabisPakaiController::class, 'store'])->name('master-barang-habis-pakai.store');
    Route::put('/master-barang-habis-pakai/{masterBarangHabisPakai}', [MasterBarangHabisPakaiController::class, 'update'])->name('master-barang-habis-pakai.update');
    Route::delete('/master-barang-habis-pakai/{masterBarangHabisPakai}', [MasterBarangHabisPakaiController::class, 'destroy'])->name('master-barang-habis-pakai.destroy');
    Route::patch('/master-barang-habis-pakai/{id}/restore', [MasterBarangHabisPakaiController::class, 'restore'])->name('master-barang-habis-pakai.restore');
    Route::delete('/master-barang-habis-pakai/{id}/force-delete', [MasterBarangHabisPakaiController::class, 'forceDelete'])->name('master-barang-habis-pakai.force-delete');
    Route::get('/master-barang-habis-pakai/export', [MasterBarangHabisPakaiController::class, 'export'])->name('master-barang-habis-pakai.export');
    Route::post('/master-barang-habis-pakai/import', [MasterBarangHabisPakaiController::class, 'import'])->name('master-barang-habis-pakai.import');
    Route::get('/master-barang-habis-pakai/template', [MasterBarangHabisPakaiController::class, 'downloadTemplate'])->name('master-barang-habis-pakai.template');
    Route::get('/master-barang-habis-pakai/download-template', [MasterBarangHabisPakaiController::class, 'downloadTemplate'])->name('master-barang-habis-pakai.download-template');
    
    // Pengadaan Barang routes
    Route::get('/pengadaan-barang', [PengadaanBarangController::class, 'index'])->name('pengadaan-barang.index');
    Route::get('/pengadaan-barang/create', [PengadaanBarangController::class, 'create'])->name('pengadaan-barang.create');
    Route::post('/pengadaan-barang', [PengadaanBarangController::class, 'store'])->name('pengadaan-barang.store');
    Route::get('/pengadaan-barang/{pengadaanBarang}', [PengadaanBarangController::class, 'show'])->name('pengadaan-barang.show');
    Route::patch('/pengadaan-barang/{pengadaanBarang}/approve', [PengadaanBarangController::class, 'approve'])->name('pengadaan-barang.approve');
    Route::patch('/pengadaan-barang/{pengadaanBarang}/reject', [PengadaanBarangController::class, 'reject'])->name('pengadaan-barang.reject');
    Route::delete('/pengadaan-barang/{pengadaanBarang}', [PengadaanBarangController::class, 'destroy'])->name('pengadaan-barang.destroy');
    
    Route::get('/master-barang-modal', [MasterBarangModalController::class, 'index'])->name('master-barang-modal.index');
    Route::post('/master-barang-modal', [MasterBarangModalController::class, 'store'])->name('master-barang-modal.store');
    Route::put('/master-barang-modal/{masterBarangModal}', [MasterBarangModalController::class, 'update'])->name('master-barang-modal.update');
    Route::delete('/master-barang-modal/{masterBarangModal}', [MasterBarangModalController::class, 'destroy'])->name('master-barang-modal.destroy');
    Route::patch('/master-barang-modal/{id}/restore', [MasterBarangModalController::class, 'restore'])->name('master-barang-modal.restore');
    Route::get('/master-barang-modal/export', [MasterBarangModalController::class, 'export'])->name('master-barang-modal.export');
    Route::post('/master-barang-modal/import', [MasterBarangModalController::class, 'import'])->name('master-barang-modal.import');
    Route::get('/master-barang-modal/template', [MasterBarangModalController::class, 'downloadTemplate'])->name('master-barang-modal.template');
    
    Route::get('/master-obat', [MasterObatController::class, 'index'])->name('master-obat.index');
    Route::post('/master-obat', [MasterObatController::class, 'store'])->name('master-obat.store');
    Route::put('/master-obat/{masterObat}', [MasterObatController::class, 'update'])->name('master-obat.update');
    Route::delete('/master-obat/{masterObat}', [MasterObatController::class, 'destroy'])->name('master-obat.destroy');
    Route::patch('/master-obat/{id}/restore', [MasterObatController::class, 'restore'])->name('master-obat.restore');
    
    Route::get('/monitoring-alkes', [MonitoringAlkesController::class, 'index'])->name('monitoring-alkes.index');
    Route::post('/monitoring-alkes', [MonitoringAlkesController::class, 'store'])->name('monitoring-alkes.store');
    Route::put('/monitoring-alkes/{monitoringAlkes}', [MonitoringAlkesController::class, 'update'])->name('monitoring-alkes.update');
    Route::delete('/monitoring-alkes/{monitoringAlkes}', [MonitoringAlkesController::class, 'destroy'])->name('monitoring-alkes.destroy');
    
    // Barang routes - Stok & Inventaris
    Route::get('/stok-habis-pakai', [StokHabisPakaiController::class, 'index'])->name('stok-habis-pakai.index');
    Route::post('/stok-habis-pakai', [StokHabisPakaiController::class, 'store'])->name('stok-habis-pakai.store');
    Route::put('/stok-habis-pakai/{stokHabisPakai}', [StokHabisPakaiController::class, 'update'])->name('stok-habis-pakai.update');
    Route::delete('/stok-habis-pakai/{stokHabisPakai}', [StokHabisPakaiController::class, 'destroy'])->name('stok-habis-pakai.destroy');
    Route::get('/stok-habis-pakai/export', [StokHabisPakaiController::class, 'export'])->name('stok-habis-pakai.export');
    
    Route::get('/detail-barang-modal', [DetailBarangModalController::class, 'index'])->name('detail-barang-modal.index');
    Route::post('/detail-barang-modal', [DetailBarangModalController::class, 'store'])->name('detail-barang-modal.store');
    Route::put('/detail-barang-modal/{detailBarangModal}', [DetailBarangModalController::class, 'update'])->name('detail-barang-modal.update');
    Route::delete('/detail-barang-modal/{detailBarangModal}', [DetailBarangModalController::class, 'destroy'])->name('detail-barang-modal.destroy');
    Route::patch('/detail-barang-modal/{detailBarangModal}/restore', [DetailBarangModalController::class, 'restore'])->name('detail-barang-modal.restore');
    Route::get('/detail-barang-modal/template', [DetailBarangModalController::class, 'downloadTemplate'])->name('detail-barang-modal.template');
    Route::post('/detail-barang-modal/import', [DetailBarangModalController::class, 'import'])->name('detail-barang-modal.import');
    Route::get('/detail-barang-modal/export', [DetailBarangModalController::class, 'export'])->name('detail-barang-modal.export');
    
    // Barang routes - Permintaan
    Route::get('/permintaan-barang/create', function () {
        return Inertia::render('Barang/PermintaanBarang/Create');
    })->name('permintaan-barang.create');
    Route::get('/permintaan-barang', [PermintaanBarangController::class, 'index'])->name('permintaan-barang.index');
    Route::post('/permintaan-barang', [PermintaanBarangController::class, 'store'])->name('permintaan-barang.store');
    Route::get('/permintaan-barang/approval', function () {
        return Inertia::render('Barang/PermintaanBarang/Approval');
    })->name('permintaan-barang.approval');
    
    // Barang routes - Laporan
    Route::get('/laporan/stok', [LaporanController::class, 'stok'])->name('laporan.stok');
    Route::get('/laporan/stok/export', [LaporanController::class, 'exportStok'])->name('laporan.stok.export');
    Route::get('/laporan/permintaan', [LaporanController::class, 'permintaan'])->name('laporan.permintaan');
    Route::get('/laporan/permintaan/export', [LaporanController::class, 'exportPermintaan'])->name('laporan.permintaan.export');
    
    // Legacy routes (untuk backward compatibility)
    Route::get('/master-barang', function () {
        return Inertia::render('Barang/MasterBarang/Index');
    })->name('master-barang.index');
    Route::get('/master-barang/chart', function () {
        return Inertia::render('Barang/Chart');
    })->name('master-barang.chart');
    Route::get('/detail-barang/{detailBarang}/log', [DetailBarangController::class, 'getLog'])->name('detail-barang.log');
});

Route::post('/master-barang', function () {
    return Inertia::render('Barang/MasterBarang/Create');
})->name('master-barang.store');

require __DIR__.'/auth.php';

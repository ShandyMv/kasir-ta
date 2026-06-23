<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\StokKeluarController;
use App\Http\Controllers\FifoMonitoringController;
use App\Http\Controllers\MinMaxAnalysisController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/user-management', [App\Http\Controllers\UserManagementController::class, 'index'])->name('user-management');
        Route::post('/user-management', [App\Http\Controllers\UserManagementController::class, 'store'])->name('user-management.store');
        Route::put('/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('user-management.destroy');

        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::put('/satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');

        Route::get('/bahan-baku', [BahanBakuController::class, 'index'])->name('bahan-baku');
        Route::post('/bahan-baku', [BahanBakuController::class, 'store'])->name('bahan-baku.store');
        Route::put('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'update'])->name('bahan-baku.update');
        Route::delete('/bahan-baku/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('bahan-baku.destroy');
    });

    // Admin + Karyawan
    Route::middleware('role:admin,karyawan')->group(function () {
        Route::get('/stok-masuk', [StokMasukController::class, 'index'])->name('stok-masuk');
        Route::post('/stok-masuk', [StokMasukController::class, 'store'])->name('stok-masuk.store');

        Route::get('/stok-keluar', [StokKeluarController::class, 'index'])->name('stok-keluar');
        Route::get('/stok-keluar/create', [StokKeluarController::class, 'create'])->name('stok-keluar.create');
        Route::post('/stok-keluar', [StokKeluarController::class, 'store'])->name('stok-keluar.store');
    });

    // Semua role
    Route::middleware('role:admin,karyawan,owner')->group(function () {
        Route::get('/fifo-monitoring', [FifoMonitoringController::class, 'index'])->name('fifo-monitoring');
        Route::delete('/fifo-monitoring/{fifoBatch}', [FifoMonitoringController::class, 'destroy'])->name('fifo-monitoring.destroy');
    });

    // Admin + Owner
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/min-max-analysis', [MinMaxAnalysisController::class, 'index'])->name('min-max-analysis');
        Route::post('/min-max-analysis/{bahanBaku}/apply', [MinMaxAnalysisController::class, 'apply'])->name('min-max-analysis.apply');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    });

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

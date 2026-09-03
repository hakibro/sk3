<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BoyongController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verifikasi-boyong/{token}', [BoyongController::class, 'verifikasi'])->name('boyong.verifikasi');

// Route Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grouping Route Auth (Profile, Siswa, Boyong)
Route::middleware('auth')->group(function () {

    // --- MANAJEMEN PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MANAJEMEN SISWA ---
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{idperson}', [SiswaController::class, 'show'])->name('siswa.show');

    // --- MANAJEMEN BOYONG ---
    // Daftar Pengajuan (Semua Role)
    Route::get('/boyong', [BoyongController::class, 'index'])->name('boyong.index');
    Route::get('/boyong/laporan', [BoyongController::class, 'laporan'])->name('boyong.laporan');

    // Form Pengajuan
    Route::get('/boyong/ajukan/{idperson}', [BoyongController::class, 'create'])->name('boyong.create');
    Route::post('/boyong/store', [BoyongController::class, 'store'])->name('boyong.store');

    // Khusus Pusat: Approve/Reject (Secara teknis hanya bisa diakses via gate/check di controller)
    Route::patch('/boyong/{id}/status', [BoyongController::class, 'updateStatus'])->name('boyong.update-status');

    // Cetak Surat
    Route::get('/boyong/{id}/cetak', [BoyongController::class, 'cetakSurat'])->name('boyong.cetak');
    Route::get('/boyong/{id}/cetak-keterangan-pengajuan', [BoyongController::class, 'cetakKeteranganPengajuan'])->name('boyong.cetak-keterangan');
});

// --- MANAJEMEN ADMIN (Prefix /admin & Gate access-admin) ---
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {

    // Halaman Utama Admin
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // Pengaturan User
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');

    // Pengaturan Alasan
    Route::post('/alasan', [AdminController::class, 'storeAlasan'])->name('admin.alasan.store');
    Route::delete('/alasan/{id}', [AdminController::class, 'deleteAlasan'])->name('admin.alasan.destroy');

    // Pengaturan Kop Surat
    Route::patch('/kop-surat', [AdminController::class, 'updateKopSurat'])->name('admin.kop-surat.update');

    // Pengaturan Cut-off Pembayaran
    Route::patch('/cut-off', [AdminController::class, 'updateCutOff'])->name('admin.cutoff.update');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BoyongController;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    return view('welcome');
});

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

    // Form Pengajuan
    Route::get('/boyong/ajukan/{idperson}', [BoyongController::class, 'create'])->name('boyong.create');
    Route::post('/boyong/store', [BoyongController::class, 'store'])->name('boyong.store');

    // Khusus Pusat: Approve/Reject (Secara teknis hanya bisa diakses via gate/check di controller)
    Route::patch('/boyong/{id}/status', [BoyongController::class, 'updateStatus'])->name('boyong.update-status');

    // Cetak Surat
    Route::get('/boyong/{id}/cetak', [BoyongController::class, 'cetakSurat'])->name('boyong.cetak');
});

// --- MANAJEMEN ADMIN (Prefix /admin & Gate access-admin) ---
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {

    // Halaman Utama Admin
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // Pengaturan User
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');

    // Pengaturan Alasan
    Route::post('/alasan', [AdminController::class, 'storeAlasan'])->name('admin.alasan.store');
    Route::delete('/alasan/{id}', [AdminController::class, 'deleteAlasan'])->name('admin.alasan.destroy');
});

require __DIR__ . '/auth.php';
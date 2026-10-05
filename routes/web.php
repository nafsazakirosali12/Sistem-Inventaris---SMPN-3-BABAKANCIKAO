<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\LoanController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profil Admin (flow.md 3.3 & DESIGN.md)
        Route::get('/profil-admin', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('/profil-admin', [AdminProfileController::class, 'update'])->name('profile.update');

        // Profil Sekolah (flow.md 3.4)
        Route::get('/profil-sekolah', [SchoolProfileController::class, 'index'])->name('profil.index');
        Route::put('/profil-sekolah', [SchoolProfileController::class, 'update'])->name('profil.update');

        // Kategori Management (flow.md 3.6)
        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

        // Ruangan Management (flow.md 3.7)
        Route::get('/ruangan', [RoomController::class, 'index'])->name('ruangan.index');
        Route::post('/ruangan', [RoomController::class, 'store'])->name('ruangan.store');
        Route::put('/ruangan/{room}', [RoomController::class, 'update'])->name('ruangan.update');
        Route::delete('/ruangan/{room}', [RoomController::class, 'destroy'])->name('ruangan.destroy');

        // Data Inventaris (flow.md 3.8 & DESIGN.md)
        Route::get('/inventaris', [InventoryController::class, 'index'])->name('inventaris.index');
        Route::get('/inventaris/tambah', [InventoryController::class, 'create'])->name('inventaris.create');
        Route::post('/inventaris', [InventoryController::class, 'store'])->name('inventaris.store');
        Route::get('/inventaris/{inventory}', [InventoryController::class, 'show'])->name('inventaris.show');
        Route::get('/inventaris/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventaris.edit');
        Route::put('/inventaris/{inventory}', [InventoryController::class, 'update'])->name('inventaris.update');
        Route::delete('/inventaris/{inventory}', [InventoryController::class, 'destroy'])->name('inventaris.destroy');

        // Data Peminjaman (flow.md 3.9 & DESIGN.md 6.11)
        Route::get('/peminjaman', [LoanController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [LoanController::class, 'store'])->name('peminjaman.store');
        Route::put('/peminjaman/{loan}', [LoanController::class, 'update'])->name('peminjaman.update');

        // Daftar Akun Peminjam (flow.md 3.11)
        Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');
        Route::put('/akun/{user}', [AccountController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{user}', [AccountController::class, 'destroy'])->name('akun.destroy');
    });
});


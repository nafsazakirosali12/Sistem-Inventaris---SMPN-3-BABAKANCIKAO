<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\SchoolProfileController;

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

        // Daftar Akun Peminjam (flow.md 3.11)
        Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');
        Route::put('/akun/{user}', [AccountController::class, 'update'])->name('akun.update');
        Route::delete('/akun/{user}', [AccountController::class, 'destroy'])->name('akun.destroy');
    });
});


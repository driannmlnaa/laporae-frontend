<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (session()->has('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login.form');
})->name('home');


Route::get('/register', [UserController::class, 'registerForm'])->name('register.form');
Route::post('/register', [UserController::class, 'registerProcess'])->name('register.process');

Route::get('/login', [UserController::class, 'loginForm'])->name('login.form');
Route::post('/login', [UserController::class, 'loginProcess'])->name('login.process');

Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
Route::get('/lapor', [LaporanController::class, 'create'])->name('lapor.create');
Route::post('/lapor', [LaporanController::class, 'store'])->name('lapor.store');
Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'editUser'])->name('laporan.edit');
Route::put('/laporan/{laporan}', [LaporanController::class, 'updateUser'])->name('laporan.update');
Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroyUser'])->name('laporan.destroy');
Route::get('/laporan/{laporan}', [LaporanController::class, 'show'])->name('laporan.show');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminController::class, 'loginProcess'])->name('admin.login.process');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/admin/laporan/{id}/edit', [AdminController::class, 'edit'])->name('admin.laporan.edit');
Route::put('/admin/laporan/{id}', [AdminController::class, 'update'])->name('admin.laporan.update');
Route::delete('/admin/laporan/{id}', [AdminController::class, 'destroy'])->name('admin.laporan.destroy');

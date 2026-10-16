<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoneyRequestController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/requests', [MoneyRequestController::class, 'store'])->middleware('throttle:5,1')->name('requests.store');
    Route::get('/admin', [AdminController::class, 'index'])->middleware('admin')->name('admin.index');
    Route::post('/admin/requests/{moneyRequest}', [AdminController::class, 'updateRequest'])->middleware('admin')->name('admin.requests.update');
    Route::post('/admin/stations', [AdminController::class, 'storeStation'])->middleware('admin')->name('admin.stations.store');
});

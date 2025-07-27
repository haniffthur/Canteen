<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ScheduleController; // Contoh controller admin
use App\Http\Controllers\Admin\MenuController;       // Contoh controller admin
use App\Http\Controllers\Admin\EmployeeController;    // Contoh controller admin
use App\Http\Controllers\Admin\GateController;        // Contoh controller admin
use App\Http\Controllers\Admin\CardController;        // Contoh controller admin
use App\Http\Controllers\Admin\UserController;        // Contoh controller admin
use App\Http\Controllers\HR\ReportController;         // Contoh controller HR

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Routes untuk Tamu (Guest) ---
// Menggunakan method yang lebih umum di Laravel: showLoginForm, login, logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// --- Halaman Utama ---
// Arahkan halaman utama ke login jika belum login, atau ke dashboard jika sudah
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});


// --- Grup Route untuk Pengguna yang Sudah Login ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    |
    | Route di bawah ini idealnya hanya bisa diakses oleh admin.
    | Kita bisa tambahkan middleware 'role:admin' di sini nanti jika diperlukan.
    |
    */
    // Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Contoh: Route untuk mengatur jadwal
        // Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        // Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        
        // Resource Controllers untuk Master Data
        // Route::resource('menus', MenuController::class);
        // Route::resource('employees', EmployeeController::class);
        // Route::resource('gates', GateController::class);
        // Route::resource('cards', CardController::class);
        // Route::resource('users', UserController::class);

    // });


    /*
    |--------------------------------------------------------------------------
    | HR Routes
    |--------------------------------------------------------------------------
    */
    // Route::middleware(['role:hr'])->prefix('hr')->name('hr.')->group(function () {
        
        // Contoh: Route untuk Laporan
        // Route::get('/reports/consumption', [ReportController::class, 'consumption'])->name('reports.consumption');

    // });

});
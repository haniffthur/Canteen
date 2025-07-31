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
use App\Http\Controllers\ReportController;         // Contoh controller HR
use App\Http\Controllers\TappingController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\MoveMenuController;
use App\Http\Controllers\WaiterViewController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Routes untuk Tamu (Guest) ---
// Menggunakan method yang lebih umum di Laravel: showLoginForm, login, logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// --- Halaman Utama ---
// Arahkan halaman utama ke login jika belum login, atau ke dashboard jika sudah
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
    // Halaman Tapping (Publik)
Route::get('/tapping/{gate}', [TappingController::class, 'index'])->name('tapping.index');

// API Untuk Proses Tapping
Route::post('/api/tap', [TappingController::class, 'tap'])->name('api.tap.process');

// ROUTE BARU: API untuk mengambil menu aktif secara dinamis
Route::get('/api/tapping/{gate}/menu', [TappingController::class, 'getActiveMenu'])->name('api.tapping.menu');

Route::get('/waiter-view/{gate}', [WaiterViewController::class, 'index'])->name('waiter-view.index');
Route::get('/api/waiter-view/{gate}/logs', [WaiterViewController::class, 'getLatestLogs'])->name('api.waiter-view.logs');
    

// --- Grup Route untuk Pengguna yang Sudah Login ---
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('api.dashboard.data');

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
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index'); // <-- ROUTE BARU
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show'); // <-- ROUTE BARU
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy'); // <-- ROUTE BARU

    // Resource Controllers untuk Master Data
    Route::resource('menus', MenuController::class);
    Route::resource('employees', EmployeeController::class);

 Route::resource('users', UserController::class);
    Route::resource('gates', GateController::class);
    Route::post('gates/bulk-update-time', [GateController::class, 'bulkUpdateTime'])->name('gates.bulkUpdateTime');

    Route::resource('cards', CardController::class);
    // Route::resource('users', UserController::class);

     Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/{employee}', [LogController::class, 'show'])->name('logs.show'); // <-- Route baru untuk detail
    Route::get('/reports/consumption', [ReportController::class, 'consumption'])->name('reports.consumption');
     Route::get('/reports/consumption/{menu}', [ReportController::class, 'consumptionDetail'])->name('reports.consumption.detail');

Route::get('/move-menu', [MoveMenuController::class, 'create'])->name('move-menu.create');
    Route::post('/move-menu', [MoveMenuController::class, 'store'])->name('move-menu.store');
    // });


    /*
    |--------------------------------------------------------------------------
    | HR Routes
    |--------------------------------------------------------------------------
    */
    // Route::middleware(['role:hr'])->prefix('hr')->name('hr.')->group(function () {

    // Contoh: Route untuk Laporan


    // });

});
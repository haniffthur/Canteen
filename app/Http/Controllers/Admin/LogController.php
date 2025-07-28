<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealLog;
use App\Models\Employee; // <-- Import model Employee
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Import DB Facade

class LogController extends Controller
{
    /**
     * Menampilkan daftar log yang sudah dikelompokkan per karyawan.
     */
    public function index()
{
    // Ambil ID log transaksi terakhir per karyawan
    $latestLogs = MealLog::selectRaw('MAX(id) as id')
        ->groupBy('employee_id');

    // Ambil data MealLog berdasarkan ID yang sudah dipilih di atas
    $logs = MealLog::with(['employee', 'counterMenu.gate'])
        ->whereIn('id', $latestLogs)
        ->orderByDesc('tapped_at')
        ->paginate(20);

    return view('logs.index', compact('logs'));
}


    /**
     * Menampilkan detail semua log transaksi untuk satu karyawan.
     */
    public function show(Employee $employee)
    {
        $logs = MealLog::where('employee_id', $employee->id)
            ->with('counterMenu.menu', 'counterMenu.gate')
            ->latest('tapped_at')
            ->paginate(15);

        return view('logs.show', compact('employee', 'logs'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gate;
use App\Models\MealSchedule;
use App\Models\MealLog;
use Carbon\Carbon;

class WaiterViewController extends Controller
{
    /**
     * Menampilkan halaman utama untuk waiter.
     */
    public function index(Gate $gate)
    {
        return view('waiter-view.index', compact('gate'));
    }

    /**
     * Mengambil data SATU log transaksi terakhir untuk ditampilkan di layar waiter.
     */
    public function getLatestLogs(Gate $gate)
    {
        $now = Carbon::now();
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';

        $activeSchedule = MealSchedule::where('meal_date', $now->toDateString())
            ->where('meal_type', $mealType)
            ->first();

        if (!$activeSchedule) {
            return response()->json(null); // Kembalikan null jika tidak ada jadwal
        }

        // 1. Ambil SATU log paling terakhir dari gate ini
        $lastLog = MealLog::whereHas('counterMenu', function ($query) use ($activeSchedule, $gate) {
                $query->where('meal_schedule_id', $activeSchedule->id)
                      ->where('gate_id', $gate->id);
            })
            ->latest('tapped_at')
            ->first();

        if (!$lastLog) {
            return response()->json(null); // Kembalikan null jika belum ada log
        }

        // 2. Ambil semua menu yang di-tap oleh karyawan tersebut pada waktu yang hampir bersamaan
        $allLogsForTransaction = MealLog::where('employee_id', $lastLog->employee_id)
            ->whereBetween('tapped_at', [
                Carbon::parse($lastLog->tapped_at)->subSeconds(3), // Toleransi 3 detik
                Carbon::parse($lastLog->tapped_at)->addSeconds(3)
            ])
            ->with('employee', 'counterMenu.menu')
            ->get();
        
        // 3. Format data menjadi satu "pesanan"
        $order = [
            'id' => $lastLog->id, // Gunakan ID log terakhir sebagai ID unik pesanan
            'employee_name' => $lastLog->employee->name,
            'tapped_at' => Carbon::parse($lastLog->tapped_at)->format('H:i:s'),
            'menus' => $allLogsForTransaction->map(function ($log) {
                return [
                    'name' => $log->counterMenu->menu->name,
                    'category' => $log->counterMenu->menu->category
                ];
            })->sortBy('category')->values()->all()
        ];

        return response()->json($order);
    }
}

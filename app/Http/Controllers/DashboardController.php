<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\MealLog;
use App\Models\MealSchedule;
use App\Models\CounterMenu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     */
    public function index()
    {
        return view('dashboard.index');
    }

    /**
     * Mengambil semua data untuk dashboard dan mengembalikannya sebagai JSON.
     */
    public function getDashboardData()
    {
        $now = Carbon::now();
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';

        // --- Data untuk Widget ---
        $activeSchedule = MealSchedule::where('meal_date', $now->toDateString())
            ->where('meal_type', $mealType)
            ->first();

        $mealsTodayCount = 0;
        if ($activeSchedule) {
            $mealsTodayCount = MealLog::whereHas('counterMenu', fn($q) => $q->where('meal_schedule_id', $activeSchedule->id))->count();
        }

        $activeEmployeesCount = Employee::where('status', 'active')->count();

        $lowStockMenus = [];
        if ($activeSchedule && $activeSchedule->day_type == 'special') {
            $lowStockMenus = CounterMenu::where('meal_schedule_id', $activeSchedule->id)
                ->whereNotNull('balance_qty')
                ->where('balance_qty', '<=', 20)
                ->with('menu', 'gate')
                ->get();
        }

        // --- Data untuk Chart Statistik (Konsumsi 7 Hari Terakhir) ---
        $consumptionLast7Days = MealLog::where('tapped_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(tapped_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Format data agar siap digunakan oleh Chart.js
        $chartLabels = [];
        $chartData = [];
        // Buat label untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->isoFormat('dddd, D MMM');
            // Cari data konsumsi untuk tanggal ini, jika tidak ada, isi dengan 0
            $consumption = $consumptionLast7Days->firstWhere('date', $date->toDateString());
            $chartData[] = $consumption ? $consumption->total : 0;
        }

        // Kembalikan semua data dalam satu response JSON
        return response()->json([
            'widgets' => [
                'mealsToday' => $mealsTodayCount,
                'activeEmployees' => $activeEmployeesCount,
                'activeSchedule' => $activeSchedule ? [
                    'meal_type' => ucfirst($activeSchedule->meal_type),
                    'meal_date' => Carbon::parse($activeSchedule->meal_date)->isoFormat('D MMM Y'),
                    'day_type' => ucfirst($activeSchedule->day_type),
                ] : null,
                'lowStockMenus' => $lowStockMenus,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
            ]
        ]);
    }
}

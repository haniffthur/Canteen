<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MealLog;
use App\Models\Menu; // <-- Import Menu Model
use App\Models\CounterMenu; // <-- PERBAIKAN DI SINI
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
   public function consumption(Request $request)
    {
        // Tentukan rentang tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Query rekap konsumsi
        $reportData = MealLog::whereBetween(DB::raw('DATE(tapped_at)'), [$startDate, $endDate])
            ->join('counter_menus', 'meal_logs.counter_menu_id', '=', 'counter_menus.id')
            ->join('menus', 'counter_menus.menu_id', '=', 'menus.id')
            ->select('menus.id as menu_id', 'menus.name as menu_name', DB::raw('count(*) as total_consumed'))
            ->groupBy('menus.id', 'menus.name')
            ->orderBy('total_consumed', 'desc')
            ->get();

        // Query sisa stok hari ini
        $todayStocks = [];
        if (Carbon::now()->between(Carbon::parse($startDate), Carbon::parse($endDate))) {
            $today = Carbon::now()->toDateString();
            $mealType = Carbon::now()->hour >= 17 ? 'dinner' : 'lunch';
            $todayStocks = CounterMenu::whereHas('mealSchedule', function ($query) use ($today, $mealType) {
                    $query->where('meal_date', $today)->where('meal_type', $mealType);
                })
                ->whereNotNull('balance_qty')
                ->select('menu_id', DB::raw('SUM(balance_qty) as remaining_stock'))
                ->groupBy('menu_id')
                ->pluck('remaining_stock', 'menu_id');
        }

        // ## PERUBAHAN LOGIKA DI SINI ##
        // Jika ini adalah permintaan AJAX, kembalikan data sebagai JSON
        if ($request->wantsJson()) {
            return response()->json([
                'reportData' => $reportData,
                'todayStocks' => $todayStocks,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'formattedStartDate' => Carbon::parse($startDate)->isoFormat('D MMM Y'),
                'formattedEndDate' => Carbon::parse($endDate)->isoFormat('D MMM Y'),
            ]);
        }
        
        // Jika bukan, tampilkan view seperti biasa
        return view('reports.consumption', compact('reportData', 'startDate', 'endDate', 'todayStocks'));
    }

    /**
     * METHOD BARU: Menampilkan detail siapa saja yang mengonsumsi menu tertentu.
     */
    public function consumptionDetail(Request $request, Menu $menu)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $logs = MealLog::whereHas('counterMenu', function ($query) use ($menu) {
                $query->where('menu_id', $menu->id);
            })
            ->whereBetween(DB::raw('DATE(tapped_at)'), [$startDate, $endDate])
            ->with('employee')
            ->latest('tapped_at')
            ->paginate(20);

        return view('reports.consumption_detail', compact('logs', 'menu', 'startDate', 'endDate'));
    }
}
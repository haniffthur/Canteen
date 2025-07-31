<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gate;
use App\Models\Card;
use App\Models\MealSchedule;
use App\Models\CounterMenu;
use App\Models\MealLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TappingController extends Controller
{
    /**
     * Menampilkan halaman antarmuka tapping untuk gate/counter tertentu.
     */
    public function index(Gate $gate)
    {
        $now = Carbon::now();
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';

        $activeSchedule = MealSchedule::where('meal_date', $now->toDateString())
            ->where('meal_type', $mealType)
            ->first();

        $activeMenus = [];
        if ($activeSchedule) {
            $activeMenus = CounterMenu::where('meal_schedule_id', $activeSchedule->id)
                ->where('gate_id', $gate->id)
                ->with('menu')
                ->get();
        }

        return view('tapping.index', compact('gate', 'activeMenus'));
    }

    /**
     * Memproses permintaan tap kartu dari antarmuka.
     */
    public function tap(Request $request)
    {
        $validated = $request->validate([
            'card_number' => 'required|string',
            'gate_id' => 'required|exists:gates,id',
            'optional_ids' => 'nullable|array',
            'optional_ids.*' => 'integer|exists:counter_menus,id',
        ]);
        
        $now = Carbon::now();
        $gate = Gate::find($validated['gate_id']);

        // Pengecekan status & jam operasional counter
        if ($gate->status !== 'active') {
            return response()->json(['success' => false, 'message' => "Counter '{$gate->name}' sedang tidak aktif."], 403);
        }
        if ($gate->start_time && $gate->stop_time) {
            if (!$now->between(Carbon::parse($gate->start_time), Carbon::parse($gate->stop_time))) {
                 return response()->json(['success' => false, 'message' => "Counter hanya beroperasi antara jam {$gate->start_time} - {$gate->stop_time}."], 403);
            }
        }
        
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';
        $card = Card::where('card_number', $validated['card_number'])->with('employee')->first();
        if (!$card || !$card->employee || $card->employee->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Kartu Tidak Terdaftar.'], 404);
        }
        $employee = $card->employee;
        
        $schedule = MealSchedule::where('meal_date', $now->toDateString())->where('meal_type', $mealType)->first();
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Tidak Ada Jadwal Makan Aktif.'], 404);
        }
        
        $alreadyConsumed = MealLog::where('employee_id', $employee->id)
            ->whereHas('counterMenu', fn($q) => $q->where('meal_schedule_id', $schedule->id))
            ->exists();
        if ($alreadyConsumed) {
            return response()->json(['success' => false, 'message' => 'Karyawan Sudah Mengambil Makan.', 'employee_name' => $employee->name], 409);
        }
        
        try {
            DB::transaction(function () use ($validated, $employee, $schedule) {
                // a. Cari dan proses menu utama
                $mainCounterMenu = CounterMenu::where('meal_schedule_id', $schedule->id)
                    ->where('gate_id', $validated['gate_id'])
                    ->whereHas('menu', fn($q) => $q->whereIn('category', ['utama', 'spesial']))
                    ->first();

                if (!$mainCounterMenu) {
                    throw new \Exception('Tidak ada menu utama/spesial yang dijadwalkan.');
                }
                
                // Cek stok menu utama (hanya jika hari spesial)
                if ($schedule->day_type == 'special' && $mainCounterMenu->balance_qty <= 0) {
                    throw new \Exception('Stok menu utama telah habis.');
                }

                MealLog::create([
                    'employee_id' => $employee->id, 
                    'counter_menu_id' => $mainCounterMenu->id,
                    'tapped_at' => now(), 
                    'status' => 'success',
                ]);
                // Kurangi stok menu utama (hanya jika hari spesial)
                if ($schedule->day_type == 'special') {
                    $mainCounterMenu->decrement('balance_qty');
                }

                // b. Loop dan proses setiap menu opsional yang dipilih
                if (!empty($validated['optional_ids'])) {
                    $optionalCounterMenus = CounterMenu::find($validated['optional_ids']);
                    foreach ($optionalCounterMenus as $optionalMenu) {
                        // Cek stok JIKA stoknya diatur (tidak null), tidak peduli tipe hari.
                        if ($optionalMenu->balance_qty !== null && $optionalMenu->balance_qty <= 0) {
                           throw new \Exception("Stok untuk menu '{$optionalMenu->menu->name}' telah habis.");
                        }
                        
                        MealLog::create([
                            'employee_id' => $employee->id, 
                            'counter_menu_id' => $optionalMenu->id,
                            'tapped_at' => now(), 
                            'status' => 'success',
                        ]);

                        // Kurangi stok JIKA stoknya diatur (tidak null).
                        if ($optionalMenu->balance_qty !== null) {
                            $optionalMenu->decrement('balance_qty');
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Pengambilan Makan Berhasil Dicatat!',
            'employee_name' => $employee->name,
        ]);
    }
    public function getActiveMenu(Gate $gate)
    {
        $now = Carbon::now();
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';

        $activeSchedule = MealSchedule::where('meal_date', $now->toDateString())
            ->where('meal_type', $mealType)
            ->first();

        $activeMenus = [];
        if ($activeSchedule) {
            $activeMenus = CounterMenu::where('meal_schedule_id', $activeSchedule->id)
                ->where('gate_id', $gate->id)
                ->with('menu') // Eager load relasi menu
                ->get();
        }

        return response()->json($activeMenus);
    }
}

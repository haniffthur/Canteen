<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MealSchedule;
use App\Models\CounterMenu;
use App\Models\Gate;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MoveMenuController extends Controller
{
    /**
     * Menampilkan form untuk memindahkan menu.
     */
    public function create()
    {
        $now = Carbon::now();
        $mealType = $now->hour >= 17 ? 'dinner' : 'lunch';

        // Cari jadwal yang sedang aktif
        $activeSchedule = MealSchedule::where('meal_date', $now->toDateString())
            ->where('meal_type', $mealType)
            ->first();

        $movableMenus = collect(); // Default collection kosong
        if ($activeSchedule) {
            // Ambil semua menu yang punya stok untuk dipindahkan
            $movableMenus = CounterMenu::where('meal_schedule_id', $activeSchedule->id)
                ->where('balance_qty', '>', 0)
                ->with('menu', 'gate')
                ->get();
        }
        
        $gates = Gate::where('status', 'active')->get();

        return view('move-menu.create', compact('activeSchedule', 'movableMenus', 'gates'));
    }

    /**
     * Memproses perpindahan menu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_counter_menu_id' => 'required|exists:counter_menus,id',
            'destination_gate_id' => 'required|exists:gates,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $originCounterMenu = CounterMenu::findOrFail($validated['origin_counter_menu_id']);
        
        // Validasi tambahan: pastikan jumlah yang dipindah tidak melebihi sisa stok
        if ($validated['quantity'] > $originCounterMenu->balance_qty) {
            return back()->withErrors(['quantity' => 'Jumlah yang dipindah melebihi sisa stok yang tersedia.'])->withInput();
        }

        // Validasi tambahan: pastikan counter asal dan tujuan tidak sama
        if ($originCounterMenu->gate_id == $validated['destination_gate_id']) {
            return back()->withErrors(['destination_gate_id' => 'Counter asal dan tujuan tidak boleh sama.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $originCounterMenu) {
                // 1. Kurangi stok dari counter asal
                $originCounterMenu->decrement('supply_qty', $validated['quantity']);
                $originCounterMenu->decrement('balance_qty', $validated['quantity']);

                // 2. Cari atau buat entri menu di counter tujuan
                $destinationCounterMenu = CounterMenu::firstOrNew([
                    'meal_schedule_id' => $originCounterMenu->meal_schedule_id,
                    'gate_id' => $validated['destination_gate_id'],
                    'menu_id' => $originCounterMenu->menu_id,
                ]);

                // Jika ini entri baru, set tipe opsinya sama dengan aslinya
                if (!$destinationCounterMenu->exists) {
                    $destinationCounterMenu->meal_option_type = $originCounterMenu->meal_option_type;
                }

                // 3. Tambah stok ke counter tujuan
                $destinationCounterMenu->increment('supply_qty', $validated['quantity']);
                $destinationCounterMenu->increment('balance_qty', $validated['quantity']);
                $destinationCounterMenu->save();

                // 4. Catat di log perpindahan stok
                StockMovement::create([
                    'menu_id' => $originCounterMenu->menu_id,
                    'quantity' => $validated['quantity'],
                    'from_gate_id' => $originCounterMenu->gate_id,
                    'to_gate_id' => $validated['destination_gate_id'],
                    'moved_by_user_id' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memindahkan menu: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('move-menu.create')->with('success', 'Menu berhasil dipindahkan!');
    }
}

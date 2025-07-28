<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gate;
use App\Models\Menu;
use App\Models\MealSchedule;
use App\Models\CounterMenu;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = MealSchedule::withCount('counterMenus')
            ->orderBy('meal_date', 'desc')->paginate(15);
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $gates = Gate::where('status', 'active')->get();
        $menus = Menu::orderBy('name')->get();
        return view('schedules.create', compact('gates', 'menus'));
    }

    public function store(Request $request)
    {
        // 1. Filter baris kosong dari form
        $filteredAssignments = [];
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (isset($assignment['menu_id']) && $assignment['menu_id'] !== null) {
                    $filteredAssignments[] = $assignment;
                }
            }
        }
        $request->merge(['assignments' => $filteredAssignments]);

        // 2. Validasi dasar
        $validated = $request->validate([
            'meal_date' => 'required|date',
            'meal_type' => 'required|in:lunch,dinner',
            'day_type' => 'required|in:normal,special',
            'assignments' => 'required|array|min:1',
            'assignments.*.gate_id' => 'required|exists:gates,id',
            'assignments.*.menu_id' => 'required|exists:menus,id',
            'assignments.*.meal_option_type' => 'required|in:default,optional',
            'assignments.*.supply_qty' => 'nullable|integer|min:0',
        ], ['assignments.required' => 'Minimal harus ada satu penugasan menu yang valid.']);

        // 3. Validasi Kustom #1: Aturan untuk Hari Spesial
        if ($validated['day_type'] == 'special') {
            $menuIds = collect($validated['assignments'])->pluck('menu_id');
            $categories = Menu::whereIn('id', $menuIds)->pluck('category');
            if ($categories->contains('utama')) {
                return back()->withErrors(['assignments' => 'Error: Untuk "Hari Spesial", Anda hanya boleh memilih menu kategori "Spesial" atau "Opsional".'])->withInput();
            }
        }

        // 4. Validasi Kustom #2: Aturan Utama vs Spesial di satu counter
        $assignmentsByGate = collect($validated['assignments'])->groupBy('gate_id');
        foreach ($assignmentsByGate as $gateId => $assignments) {
            $menuIdsOnGate = $assignments->pluck('menu_id');
            $categoriesOnGate = Menu::whereIn('id', $menuIdsOnGate)->pluck('category');
            if ($categoriesOnGate->contains('utama') && $categoriesOnGate->contains('spesial')) {
                $gateName = Gate::find($gateId)->name;
                return back()->withErrors(['assignments' => "Error di '$gateName': Tidak boleh ada menu 'Utama' dan 'Spesial' di counter yang sama."])->withInput();
            }
        }

        // 5. Cek duplikasi jadwal
        $existingSchedule = MealSchedule::where('meal_date', $validated['meal_date'])
            ->where('meal_type', $validated['meal_type'])->first();
        if ($existingSchedule) {
            return back()->withErrors(['meal_date' => 'Jadwal untuk tanggal dan sesi makan ini sudah ada.'])->withInput();
        }

        // 6. Simpan ke Database
        DB::transaction(function () use ($validated) {
            $mealSchedule = MealSchedule::create([
                'meal_date' => $validated['meal_date'],
                'meal_type' => $validated['meal_type'],
                'day_type' => $validated['day_type'],
            ]);
            foreach ($validated['assignments'] as $assign) {
                $supplyQty = $assign['supply_qty'] ?? null;
                CounterMenu::create([
                    'meal_schedule_id' => $mealSchedule->id,
                    'gate_id' => $assign['gate_id'],
                    'menu_id' => $assign['menu_id'],
                    'meal_option_type' => $assign['meal_option_type'],
                    'supply_qty' => $supplyQty,
                    'balance_qty' => $supplyQty,
                ]);
            }
        });

        return redirect()->route('schedules.create')->with('success', 'Jadwal makan berhasil dibuat!');
    }

    public function show(MealSchedule $schedule)
    {
        $schedule->load('counterMenus.gate', 'counterMenus.menu');
        return view('schedules.show', compact('schedule'));
    }

    public function destroy(MealSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
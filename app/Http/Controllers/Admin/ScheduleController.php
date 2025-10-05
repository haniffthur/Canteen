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
    /**
     * Menampilkan daftar jadwal dan form modal untuk membuat jadwal baru.
     */
    public function index()
    {
        // Data untuk tabel utama
        $schedules = MealSchedule::withCount('counterMenus')
            ->orderBy('meal_date', 'desc')->paginate(15);
        
        // Data untuk dikirim ke modal
        $gates = Gate::where('status', 'active')->orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();

        return view('schedules.index', compact('schedules', 'gates', 'menus'));
    }

    /**
     * Menyimpan jadwal baru dari form modal.
     */
    public function store(Request $request)
    {
        // 1. Filter baris menu yang kosong
        $filteredAssignments = [];
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (isset($assignment['menu_id']) && $assignment['menu_id'] !== null) {
                    $filteredAssignments[] = $assignment;
                }
            }
        }
        $request->merge(['assignments' => $filteredAssignments]);

        // 2. Validasi
        $validated = $request->validate([
            'meal_date' => 'required|date',
            'meal_type' => 'required|in:lunch,dinner',
            'day_type' => 'required|in:normal,special',
            'assignments' => 'required|array|min:1',
            'assignments.*.menu_id' => 'required|exists:menus,id',
            'assignments.*.meal_option_type' => 'required|in:default,optional',
            'assignments.*.supply_qty' => 'nullable|integer|min:0',
            'apply_to_gates' => 'required|in:all,selected',
            'gate_ids' => 'required_if:apply_to_gates,selected|array|min:1',
            'gate_ids.*' => 'exists:gates,id',
        ], [
            'gate_ids.required_if' => 'Anda harus memilih setidaknya satu counter jika tidak menerapkan ke semua.',
            'assignments.required' => 'Minimal harus ada satu menu yang ditugaskan.'
        ]);

        // 3. Validasi Kustom
        $allAssignedMenuIds = collect($validated['assignments'])->pluck('menu_id');
        $allCategories = Menu::whereIn('id', $allAssignedMenuIds)->pluck('category');

        // Aturan #1: Hari Spesial
        if ($validated['day_type'] == 'special') {
            if ($allCategories->contains('utama')) {
                return back()->withErrors(['assignments' => 'Error: Untuk "Hari Spesial", Anda hanya boleh memilih menu kategori "Spesial" atau "Opsional".'])->withInput();
            }
        }

        // Aturan #2: Utama vs Spesial di satu counter
        if ($allCategories->contains('utama') && $allCategories->contains('spesial')) {
            return back()->withErrors(['assignments' => "Error: Tidak boleh ada menu 'Utama' dan 'Spesial' dalam satu jadwal yang sama."])->withInput();
        }
        
        // 4. Cek duplikasi jadwal
        $existingSchedule = MealSchedule::where('meal_date', $validated['meal_date'])
            ->where('meal_type', $validated['meal_type'])->first();
        if ($existingSchedule) {
            return back()->withErrors(['meal_date' => 'Jadwal untuk tanggal dan sesi makan ini sudah ada.'])->withInput();
        }

        // 5. Simpan ke Database
        DB::transaction(function () use ($validated) {
            $mealSchedule = MealSchedule::create([
                'meal_date' => $validated['meal_date'],
                'meal_type' => $validated['meal_type'],
                'day_type' => $validated['day_type'],
            ]);

            $gateIdsToApply = [];
            if ($validated['apply_to_gates'] == 'all') {
                $gateIdsToApply = Gate::where('status', 'active')->pluck('id')->toArray();
            } else {
                $gateIdsToApply = $validated['gate_ids'];
            }

            foreach ($validated['assignments'] as $menuAssignment) {
                foreach ($gateIdsToApply as $gateId) {
                    CounterMenu::create([
                        'meal_schedule_id' => $mealSchedule->id,
                        'gate_id' => $gateId,
                        'menu_id' => $menuAssignment['menu_id'],
                        'meal_option_type' => $menuAssignment['meal_option_type'],
                        'supply_qty' => $menuAssignment['supply_qty'],
                        'balance_qty' => $menuAssignment['supply_qty'],
                    ]);
                }
            }
        });

        return redirect()->route('schedules.index')->with('success', 'Jadwal makan berhasil dibuat!');
    }

    /**
     * Menampilkan detail dari satu jadwal makan.
     */
    public function show(MealSchedule $schedule)
    {
        // Ambil data counter menu dan kelompokkan berdasarkan gate_id
        $groupedCounterMenus = $schedule->counterMenus()
            ->with('gate', 'menu') // Eager load relasi
            ->get()
            ->groupBy('gate.name'); // Kelompokkan berdasarkan nama gate

        return view('schedules.show', [
            'schedule' => $schedule,
            'groupedCounterMenus' => $groupedCounterMenus
        ]);
    }

    /**
     * Menghapus jadwal makan dari database.
     */
    public function destroy(MealSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function edit(MealSchedule $schedule)
    {
        // Kita perlu memuat relasi counterMenus beserta menu dan gate-nya
        // dan mengelompokkannya agar mudah diolah oleh JavaScript.
        $schedule->load(['counterMenus' => function ($query) {
            $query->select('meal_schedule_id', 'menu_id', 'gate_id', 'meal_option_type', 'supply_qty')
                  ->distinct();
        }]);
        
        // Ambil gate_ids yang terhubung dengan jadwal ini
        $gateIds = $schedule->counterMenus->pluck('gate_id')->unique()->values();

        // Ambil menu assignments yang unik
        $assignments = $schedule->counterMenus->unique('menu_id')->map(function ($item) {
            return [
                'menu_id' => $item->menu_id,
                'meal_option_type' => $item->meal_option_type,
                'supply_qty' => $item->supply_qty,
            ];
        })->values();

        return response()->json([
            'schedule' => $schedule,
            'gate_ids' => $gateIds,
            'assignments' => $assignments
        ]);
    }

    /**
     * BARU: Memperbarui data jadwal di database.
     */
    public function update(Request $request, MealSchedule $schedule)
    {
        // Logika validasi sebagian besar sama dengan store()
        // Namun, kita perlu menyesuaikan beberapa aturan, seperti cek duplikasi.
        // Untuk saat ini, kita akan gunakan validasi yang mirip.
        $validated = $request->validate([
            'meal_date' => 'required|date',
            'meal_type' => 'required|in:lunch,dinner',
            'day_type' => 'required|in:normal,special',
            'assignments' => 'required|array|min:1',
            'assignments.*.menu_id' => 'required|exists:menus,id',
            'assignments.*.meal_option_type' => 'required|in:default,optional',
            'assignments.*.supply_qty' => 'nullable|integer|min:0',
            'apply_to_gates' => 'required|in:all,selected',
            'gate_ids' => 'required_if:apply_to_gates,selected|array|min:1',
            'gate_ids.*' => 'exists:gates,id',
        ]);

        // Cek duplikasi, abaikan jadwal yang sedang diedit
        $existingSchedule = MealSchedule::where('meal_date', $validated['meal_date'])
            ->where('meal_type', $validated['meal_type'])
            ->where('id', '!=', $schedule->id) // Abaikan diri sendiri
            ->first();
        if ($existingSchedule) {
            return back()->withErrors(['meal_date' => 'Jadwal lain untuk tanggal dan sesi ini sudah ada.'])->withInput();
        }
        
        DB::transaction(function () use ($validated, $schedule) {
            // 1. Update data utama MealSchedule
            $schedule->update([
                'meal_date' => $validated['meal_date'],
                'meal_type' => $validated['meal_type'],
                'day_type' => $validated['day_type'],
            ]);

            // 2. Hapus semua penugasan menu lama yang terkait
            $schedule->counterMenus()->delete();
            
            // 3. Buat ulang penugasan menu yang baru (logika sama seperti store)
            $gateIdsToApply = ($validated['apply_to_gates'] == 'all')
                ? Gate::where('status', 'active')->pluck('id')->toArray()
                : $validated['gate_ids'];

            foreach ($validated['assignments'] as $menuAssignment) {
                foreach ($gateIdsToApply as $gateId) {
                    CounterMenu::create([
                        'meal_schedule_id' => $schedule->id,
                        'gate_id' => $gateId,
                        'menu_id' => $menuAssignment['menu_id'],
                        'meal_option_type' => $menuAssignment['meal_option_type'],
                        'supply_qty' => $menuAssignment['supply_qty'],
                        'balance_qty' => $menuAssignment['supply_qty'],
                    ]);
                }
            }
        });

        return redirect()->route('schedules.index')->with('success', 'Jadwal makan berhasil diperbarui!');
    }
}

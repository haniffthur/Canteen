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

        if ($validated['day_type'] == 'special') {
            if ($allCategories->contains('utama')) {
                return back()->withErrors(['assignments' => 'Error: Untuk "Hari Spesial", Anda hanya boleh memilih menu kategori "Spesial" atau "Opsional".'])->withInput();
            }
        }

        if ($allCategories->contains('utama') && $allCategories->contains('spesial')) {
            return back()->withErrors(['assignments' => "Error: Tidak boleh ada menu 'Utama' dan 'Spesial' dalam satu jadwal yang sama."])->withInput();
        }

        // 4. Cek duplikasi jadwal
        $existingSchedule = MealSchedule::where('meal_date', $validated['meal_date'])
            ->where('meal_type', $validated['meal_type'])->first();
        if ($existingSchedule) {
            return back()->withErrors(['meal_date' => 'Jadal untuk tanggal dan sesi makan ini sudah ada.'])->withInput();
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
                        // PERBAIKAN: Gunakan ?? null
                        'supply_qty' => $menuAssignment['supply_qty'] ?? null,
                        'balance_qty' => $menuAssignment['supply_qty'] ?? null,
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
        $groupedCounterMenus = $schedule->counterMenus()
            ->with('gate', 'menu')
            ->get()
            ->groupBy('gate.name');

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
        DB::transaction(function () use ($schedule) {
            // Hapus counter menu terkait dulu (jika menggunakan soft delete atau perlu event)
            // $schedule->counterMenus()->delete(); // Uncomment jika perlu
            $schedule->delete(); // Hapus jadwal utama
        });
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Mengambil data jadwal untuk form edit (via AJAX/Fetch).
     */
    public function edit(MealSchedule $schedule)
    {
        $schedule->load(['counterMenus' => function ($query) {
            $query->select('meal_schedule_id', 'menu_id', 'gate_id', 'meal_option_type', 'supply_qty')
                  ->distinct();
        }]);

        $gateIds = $schedule->counterMenus->pluck('gate_id')->unique()->values();

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
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, MealSchedule $schedule)
    {
         // 1. Filter baris menu yang kosong (sama seperti store)
        $filteredAssignments = [];
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (isset($assignment['menu_id']) && $assignment['menu_id'] !== null) {
                    $filteredAssignments[] = $assignment;
                }
            }
        }
        $request->merge(['assignments' => $filteredAssignments]);

        // 2. Validasi (sama seperti store)
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

        // 3. Validasi Kustom (sama seperti store)
        $allAssignedMenuIds = collect($validated['assignments'])->pluck('menu_id');
        $allCategories = Menu::whereIn('id', $allAssignedMenuIds)->pluck('category');

        if ($validated['day_type'] == 'special') {
            if ($allCategories->contains('utama')) {
                return back()->withErrors(['assignments' => 'Error: Untuk "Hari Spesial", Anda hanya boleh memilih menu kategori "Spesial" atau "Opsional".'])->withInput();
            }
        }
        if ($allCategories->contains('utama') && $allCategories->contains('spesial')) {
            return back()->withErrors(['assignments' => "Error: Tidak boleh ada menu 'Utama' dan 'Spesial' dalam satu jadwal yang sama."])->withInput();
        }

        // 4. Cek duplikasi, abaikan jadwal yang sedang diedit
        $existingSchedule = MealSchedule::where('meal_date', $validated['meal_date'])
            ->where('meal_type', $validated['meal_type'])
            ->where('id', '!=', $schedule->id) // Abaikan diri sendiri
            ->first();
        if ($existingSchedule) {
            return back()->withErrors(['meal_date' => 'Jadwal lain untuk tanggal dan sesi ini sudah ada.'])->withInput();
        }

        // 5. Update Database
        DB::transaction(function () use ($validated, $schedule) {
            // 1. Update data utama MealSchedule
            $schedule->update([
                'meal_date' => $validated['meal_date'],
                'meal_type' => $validated['meal_type'],
                'day_type' => $validated['day_type'],
            ]);

            // 2. Hapus semua penugasan menu lama yang terkait
            $schedule->counterMenus()->delete();

            // 3. Buat ulang penugasan menu yang baru
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
                        // PERBAIKAN: Gunakan ?? null
                        'supply_qty' => $menuAssignment['supply_qty'] ?? null,
                        'balance_qty' => $menuAssignment['supply_qty'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('schedules.index')->with('success', 'Jadwal makan berhasil diperbarui!');
    }
}
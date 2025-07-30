<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gate;
use Illuminate\Http\Request;

class GateController extends Controller
{
     public function index()
    {
        // Ambil data gate yang dipaginasi untuk tabel utama
        $paginatedGates = Gate::latest()->paginate(10);
        
        // Ambil SEMUA gate untuk ditampilkan di dalam modal
        $allGates = Gate::orderBy('name')->get();

        return view('gates.index', [
            'gates' => $paginatedGates,
            'allGates' => $allGates
        ]);
    }

    public function create()
    {
        return view('gates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:gates,name',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,maintenance,inactive',
            'start_time' => 'nullable|date_format:H:i', // Validasi format jam
    'stop_time' => 'nullable|date_format:H:i|after:start_time', // Harus setelah jam mulai
        ]);

        Gate::create($validated);

        return redirect()->route('gates.index')->with('success', 'Counter berhasil ditambahkan.');
    }

    public function edit(Gate $gate)
    {
        return view('gates.edit', compact('gate'));
    }

    public function update(Request $request, Gate $gate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:gates,name,' . $gate->id,
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,maintenance,inactive',
            'start_time' => 'nullable|date_format:H:i', // Validasi format jam
    'stop_time' => 'nullable|date_format:H:i|after:start_time', // Harus setelah jam mulai
        ]);

        $gate->update($validated);

        return redirect()->route('gates.index')->with('success', 'Counter berhasil diperbarui.');
    }

    public function destroy(Gate $gate)
    {
        $gate->delete();
        return redirect()->route('gates.index')->with('success', 'Counter berhasil dihapus.');
    }
      public function bulkUpdateTime(Request $request)
    {
        $validated = $request->validate([
            'gate_ids'   => 'required|array|min:1',
            'gate_ids.*' => 'exists:gates,id',
            'start_time' => 'required|date_format:H:i',
            'stop_time'  => 'required|date_format:H:i|after:start_time',
        ], [
            'gate_ids.required' => 'Anda harus memilih setidaknya satu gate.',
        ]);

        // Update semua gate yang dipilih dalam satu query
        Gate::whereIn('id', $validated['gate_ids'])->update([
            'start_time' => $validated['start_time'],
            'stop_time'  => $validated['stop_time'],
        ]);

        return redirect()->route('gates.index')->with('success', 'Jam operasional untuk gate yang dipilih berhasil diperbarui.');
    }
}
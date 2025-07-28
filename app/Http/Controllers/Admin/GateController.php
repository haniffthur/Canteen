<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gate;
use Illuminate\Http\Request;

class GateController extends Controller
{
    public function index()
    {
        $gates = Gate::latest()->paginate(10);
        return view('gates.index', compact('gates'));
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
}
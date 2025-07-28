<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Employee;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        // Ambil data kartu beserta relasi karyawannya
        $cards = Card::with('employee')->latest()->paginate(10);
        return view('cards.index', compact('cards'));
    }

   public function create(Request $request)
{
    $employees = Employee::where('status', 'active')
        ->whereDoesntHave('card')
        ->get();
        
    // Ambil employee_id dari URL jika ada
    $selectedEmployeeId = $request->query('employee_id');

    return view('cards.create', compact('employees', 'selectedEmployeeId'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_number' => 'required|string|max:255|unique:cards,card_number',
            'employee_id' => 'required|exists:employees,id|unique:cards,employee_id',
            'status' => 'required|in:active,inactive,lost',
        ]);

        Card::create($validated);

        return redirect()->route('cards.index')->with('success', 'Kartu berhasil didaftarkan dan ditugaskan.');
    }

    public function edit(Card $card)
    {
        // Ambil karyawan yang belum punya kartu, DITAMBAH karyawan yang saat ini memegang kartu ini
        $employees = Employee::where('status', 'active')
            ->whereDoesntHave('card')
            ->orWhere('id', $card->employee_id)
            ->get();

        return view('cards.edit', compact('card', 'employees'));
    }

    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'card_number' => 'required|string|max:255|unique:cards,card_number,' . $card->id,
            'employee_id' => 'required|exists:employees,id|unique:cards,employee_id,' . $card->id,
            'status' => 'required|in:active,inactive,lost',
        ]);

        $card->update($validated);

        return redirect()->route('cards.index')->with('success', 'Data kartu berhasil diperbarui.');
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Kartu berhasil dihapus dari sistem.');
    }
}
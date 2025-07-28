<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Card; // <-- Jangan lupa tambahkan ini
use Illuminate\Support\Facades\DB; // <-- Dan ini

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

     public function store(Request $request)
    {
        // Pisahkan validasi agar bisa menangani card_number secara kondisional
        $validatedEmployee = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255|unique:employees,employee_id',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,resigned',
        ]);

        // Validasi nomor kartu HANYA jika diisi
        if ($request->filled('card_number')) {
            $request->validate([
                'card_number' => 'required|string|max:255|unique:cards,card_number',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $validatedEmployee) {
                // 1. Buat data karyawan
                $employee = Employee::create($validatedEmployee);

                // 2. Jika field nomor kartu diisi, buat juga data kartunya
                if ($request->filled('card_number')) {
                    Card::create([
                        'employee_id' => $employee->id,
                        'card_number' => $request->card_number,
                        'status' => 'active', // Default status kartu baru
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Jika ada error (misal: koneksi db putus), batalkan semua dan kembali dengan error
            return back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.')->withInput();
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        // Gunakan load() untuk memuat relasi kartu, ini lebih efisien
        $employee->load('card'); 
        
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:255|unique:employees,employee_id,' . $employee->id,
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employee->id,
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,resigned',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        // Tambahkan validasi jika karyawan masih terhubung dengan kartu, dll. (opsional)
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
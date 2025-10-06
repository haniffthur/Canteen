<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Card;
use App\Models\Department; // <-- BARU: Import model Department
use Illuminate\Support\Facades\DB;

class EmployeeAndCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cara yang lebih aman untuk menghapus data dengan adanya foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Card::truncate();
        Employee::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $employeesData = [
            [
                'name' => 'Budi Santoso',
                'employee_id' => 'EMP001',
                'email' => 'budi.santoso@example.com',
                'department' => 'IT (Information Technology)', // Sesuaikan dengan nama di DepartmentSeeder
                'card_number' => 'A1B2C3D4'
            ],
            [
                'name' => 'Citra Lestari',
                'employee_id' => 'EMP002',
                'email' => 'citra.lestari@example.com',
                'department' => 'Finance & Accounting',
                'card_number' => 'E5F6G7H8'
            ],
            [
                'name' => 'Doni Firmansyah',
                'employee_id' => 'EMP003',
                'email' => 'doni.firmansyah@example.com',
                'department' => 'HRD (Human Resources Development)',
                'card_number' => 'I9J0K1L2'
            ],
            // ... Tambahkan data karyawan lain jika perlu
        ];

        // Ambil semua departemen sekali saja untuk efisiensi
        $departments = Department::all()->keyBy('name');

        foreach ($employeesData as $data) {
            // Cari department_id berdasarkan nama.
            // Jika tidak ketemu, department_id akan menjadi null.
            $departmentId = $departments->get($data['department'])->id ?? null;

            DB::transaction(function () use ($data, $departmentId) {
                // Buat data karyawan dengan department_id
                $employee = Employee::create([
                    'name' => $data['name'],
                    'employee_id' => $data['employee_id'],
                    'email' => $data['email'],
                    'department_id' => $departmentId, // <-- DIUBAH
                    'status' => 'active',
                ]);

                // Buat dan tautkan kartu ke karyawan
                Card::create([
                    'employee_id' => $employee->id,
                    'card_number' => $data['card_number'],
                    'status' => 'active',
                ]);
            });
        }
    }
}
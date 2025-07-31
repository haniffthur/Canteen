<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Card;
use Illuminate\Support\Facades\DB;

class EmployeeAndCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikasi jika seeder dijalankan lagi
        // Beri komentar jika tidak ingin data lama terhapus
        DB::table('cards')->delete();
        DB::table('employees')->delete();

        $employeesData = [
            [
                'name' => 'Budi Santoso',
                'employee_id' => 'EMP001',
                'email' => 'budi.santoso@example.com',
                'department' => 'IT',
                'card_number' => 'A1B2C3D4'
            ],
            [
                'name' => 'Citra Lestari',
                'employee_id' => 'EMP002',
                'email' => 'citra.lestari@example.com',
                'department' => 'Finance',
                'card_number' => 'E5F6G7H8'
            ],
            [
                'name' => 'Doni Firmansyah',
                'employee_id' => 'EMP003',
                'email' => 'doni.firmansyah@example.com',
                'department' => 'HR',
                'card_number' => 'I9J0K1L2'
            ],
            [
                'name' => 'Eka Putri',
                'employee_id' => 'EMP004',
                'email' => 'eka.putri@example.com',
                'department' => 'Marketing',
                'card_number' => 'M3N4O5P6'
            ],
            [
                'name' => 'Fajar Nugroho',
                'employee_id' => 'EMP005',
                'email' => 'fajar.nugroho@example.com',
                'department' => 'IT',
                'card_number' => 'Q7R8S9T0'
            ],
            [
                'name' => 'Gita Wulandari',
                'employee_id' => 'EMP006',
                'email' => 'gita.wulandari@example.com',
                'department' => 'Finance',
                'card_number' => 'U1V2W3X4'
            ],
            [
                'name' => 'Hadi Prasetyo',
                'employee_id' => 'EMP007',
                'email' => 'hadi.prasetyo@example.com',
                'department' => 'Operations',
                'card_number' => 'Y5Z6A7B8'
            ],
            [
                'name' => 'Indah Permata',
                'employee_id' => 'EMP008',
                'email' => 'indah.permata@example.com',
                'department' => 'Marketing',
                'card_number' => 'C9D0E1F2'
            ],
            [
                'name' => 'Joko Susilo',
                'employee_id' => 'EMP009',
                'email' => 'joko.susilo@example.com',
                'department' => 'IT',
                'card_number' => 'G3H4I5J6'
            ],
            [
                'name' => 'Kartika Sari',
                'employee_id' => 'EMP010',
                'email' => 'kartika.sari@example.com',
                'department' => 'HR',
                'card_number' => 'K7L8M9N0'
            ],
        ];

        foreach ($employeesData as $data) {
            DB::transaction(function () use ($data) {
                // Buat data karyawan
                $employee = Employee::create([
                    'name' => $data['name'],
                    'employee_id' => $data['employee_id'],
                    'email' => $data['email'],
                    'department' => $data['department'],
                    'status' => 'active',
                ]);

                // Buat dan tautkan kartu ke karyawan yang baru dibuat
                Card::create([
                    'employee_id' => $employee->id,
                    'card_number' => $data['card_number'],
                    'status' => 'active',
                ]);
            });
        }
    }
}

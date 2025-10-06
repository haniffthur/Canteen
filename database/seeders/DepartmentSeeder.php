<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department; // <-- Jangan lupa import model
use Illuminate\Support\Facades\DB; // <-- Import DB facade

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel dulu untuk menghindari duplikasi saat seeding ulang
        DB::table('departments')->truncate();

        // Daftar departemen yang akan dimasukkan
        $departments = [
            ['name' => 'IT (Information Technology)'],
            ['name' => 'HRD (Human Resources Development)'],
            ['name' => 'Finance & Accounting'],
            ['name' => 'Marketing'],
            ['name' => 'Sales'],
            ['name' => 'General Affairs (GA)'],
            ['name' => 'Production'],
        ];

        // Looping dan masukkan data ke database
        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
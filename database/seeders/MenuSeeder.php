<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Menu Utama (Normal Day)
        Menu::create(['name' => 'Nasi Ayam Bakar', 'category' => 'utama']);
        Menu::create(['name' => 'Nasi Ikan Goreng', 'category' => 'utama']);
        Menu::create(['name' => 'Nasi Rendang Sapi', 'category' => 'utama']);

        // Menu Tambahan / Opsional
        Menu::create(['name' => 'Kerupuk', 'category' => 'opsional']);
        Menu::create(['name' => 'Buah Potong', 'category' => 'opsional']);
        
        // Menu Special Day
        Menu::create(['name' => 'Gado-gado', 'category' => 'spesial']);
        Menu::create(['name' => 'Baso', 'category' => 'spesial']);
        Menu::create(['name' => 'Laksa Betawi', 'category' => 'spesial']);
        Menu::create(['name' => 'Sate Ayam', 'category' => 'spesial']);
        Menu::create(['name' => 'Sate Kambing', 'category' => 'spesial']);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gate;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        Gate::create(['name' => 'Counter 1', 'location' => 'Kantin Utama']);
        Gate::create(['name' => 'Counter 2', 'location' => 'Kantin Utama']);
        Gate::create(['name' => 'Counter 3', 'location' => 'Kantin Utama']);
        Gate::create(['name' => 'Counter 4', 'location' => 'Kantin Utama']);
        Gate::create(['name' => 'Counter 5', 'location' => 'Kantin Utama']);
    }
}
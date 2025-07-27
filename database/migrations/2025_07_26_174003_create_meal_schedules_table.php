<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('meal_date');
            $table->enum('meal_type', ['lunch', 'dinner']);
            $table->enum('day_type', ['normal', 'special']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_schedules');
    }
};
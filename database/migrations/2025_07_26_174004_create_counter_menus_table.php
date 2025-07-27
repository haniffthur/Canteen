<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counter_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->constrained('gates')->onDelete('cascade');
            $table->foreignId('meal_schedule_id')->constrained('meal_schedules')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->enum('meal_option_type', ['default', 'optional'])->default('default');
            $table->integer('supply_qty')->nullable();
            $table->integer('balance_qty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counter_menus');
    }
};
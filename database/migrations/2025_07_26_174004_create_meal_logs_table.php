<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('counter_menu_id')->constrained('counter_menus')->onDelete('cascade');
            $table->timestamp('tapped_at');
            $table->enum('status', ['success', 'denied_duplicate', 'denied_no_stock', 'denied_inactive_card']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_logs');
    }
};
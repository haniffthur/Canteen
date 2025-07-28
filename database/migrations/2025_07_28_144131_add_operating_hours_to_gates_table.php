<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gates', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('status');
            $table->time('stop_time')->nullable()->after('start_time');
        });
    }

    public function down(): void
    {
        Schema::table('gates', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'stop_time']);
        });
    }
};
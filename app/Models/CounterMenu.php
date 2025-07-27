<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounterMenu extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke counter/gate.
    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }

    // Relasi ke jadwal umum.
    public function mealSchedule(): BelongsTo
    {
        return $this->belongsTo(MealSchedule::class);
    }

    // Relasi ke menu spesifik.
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
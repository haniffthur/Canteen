<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLog extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    // Casting tipe data untuk `tapped_at`.
    protected $casts = [
        'tapped_at' => 'datetime',
    ];

    // Relasi ke karyawan yang makan.
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    
    // Relasi ke menu di counter yang diambil.
    public function counterMenu(): BelongsTo
    {
        return $this->belongsTo(CounterMenu::class);
    }
}
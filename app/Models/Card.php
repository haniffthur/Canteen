<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Satu kartu dimiliki oleh satu karyawan.
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Relasi ke menu yang dipindahkan.
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    // Relasi ke user admin yang memindahkan.
    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by_user_id');
    }

    // Relasi ke counter asal.
    public function fromGate(): BelongsTo
    {
        return $this->belongsTo(Gate::class, 'from_gate_id');
    }

    // Relasi ke counter tujuan.
    public function toGate(): BelongsTo
    {
        return $this->belongsTo(Gate::class, 'to_gate_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealSchedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Satu jadwal umum bisa memiliki banyak menu di counter.
    public function counterMenus(): HasMany
    {
        return $this->hasMany(CounterMenu::class);
    }
}
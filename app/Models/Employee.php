<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Satu karyawan memiliki satu kartu.
    public function card(): HasOne
    {
        return $this->hasOne(Card::class);
    }

    // Satu karyawan bisa memiliki banyak log makan.
    public function mealLogs(): HasMany
    {
        return $this->hasMany(MealLog::class);
    }
    public function department()
{
    return $this->belongsTo(Department::class);
}

}
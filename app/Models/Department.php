<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke model Employee.
     * Satu departemen bisa memiliki banyak karyawan.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_distance_km',
        'base_charge',
        'per_km_charge',
        'academic_year',
    ];

    protected $casts = [
        'base_distance_km' => 'decimal:2',
        'base_charge' => 'decimal:2',
        'per_km_charge' => 'decimal:2',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
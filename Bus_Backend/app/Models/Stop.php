<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'name',
        'pickup_time',
        'drop_time',
        'academic_year',
        'distance_from_start_km',
    ];

    protected $casts = [
        'distance_from_start_km' => 'decimal:2',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    
    public function studentsPickup()
    {
        return $this->hasMany(Student::class, 'pickup_stop_id');
    }
    
    public function studentsDrop()
    {
        return $this->hasMany(Student::class, 'drop_stop_id');
    }
    
    public function studentRoutes()
    {
        return $this->hasMany(StudentRoute::class);
    }
}
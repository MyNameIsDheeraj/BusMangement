<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'registration_no',
        'model',
        'seating_capacity',
        'status',
        'driver_id',
        'cleaner_id',
    ];

    protected $casts = [
        'seating_capacity' => 'integer',
        'status' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    
    public function cleaner()
    {
        return $this->belongsTo(User::class, 'cleaner_id');
    }
    
    public function routes()
    {
        return $this->hasMany(Route::class);
    }
    
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class, 'bus_id');
    }
    
    public function attendances()
    {
        return $this->hasMany(BusAttendance::class);
    }
    
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
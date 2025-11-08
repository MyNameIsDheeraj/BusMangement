<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'name',
        'total_kilometer',
        'start_time',
        'end_time',
        'academic_year',
    ];

    protected $casts = [
        'total_kilometer' => 'decimal:2',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    
    public function stops()
    {
        return $this->hasMany(Stop::class);
    }
    
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
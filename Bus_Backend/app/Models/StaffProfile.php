<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'salary',
        'emergency_contact',
        'bus_id',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
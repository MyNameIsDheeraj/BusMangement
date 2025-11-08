<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_id',
        'admission_no',
        'dob',
        'address',
        'pickup_stop_id',
        'drop_stop_id',
        'bus_service_active',
        'academic_year',
    ];

    protected $casts = [
        'dob' => 'date',
        'bus_service_active' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    public function pickupStop()
    {
        return $this->belongsTo(Stop::class, 'pickup_stop_id');
    }
    
    public function dropStop()
    {
        return $this->belongsTo(Stop::class, 'drop_stop_id');
    }
    
    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent', 'student_id', 'parent_id');
    }
    
    public function studentRoutes()
    {
        return $this->hasMany(StudentRoute::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function attendances()
    {
        return $this->hasMany(BusAttendance::class);
    }
    
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'student_id');
    }
    
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
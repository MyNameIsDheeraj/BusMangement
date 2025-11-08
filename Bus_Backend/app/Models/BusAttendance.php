<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bus_id',
        'date',
        'status',
        'marked_by',
        'academic_year',
    ];

    protected $casts = [
        'date' => 'date',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    
    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
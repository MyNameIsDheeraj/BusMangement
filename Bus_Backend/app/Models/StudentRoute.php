<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'stop_id',
        'type',
        'is_active',
        'academic_year',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function stop()
    {
        return $this->belongsTo(Stop::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'class',
        'academic_year',
        'class_teacher_id',
    ];
    
    protected $table = 'classes';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function teacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }
    
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
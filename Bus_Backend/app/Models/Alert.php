<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by',
        'student_id',
        'bus_id',
        'route_id',
        'description',
        'severity',
        'media_path',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'audience',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
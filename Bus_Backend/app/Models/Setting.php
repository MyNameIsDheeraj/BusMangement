<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'display_name',
        'description',
        'data_type',
        'validation_rule',
        'is_system_locked',
        'is_visible',
    ];

    protected $casts = [
        'is_system_locked' => 'boolean',
        'is_visible' => 'boolean',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
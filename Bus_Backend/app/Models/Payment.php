<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount_paid',
        'total_amount_due',
        'payment_type',
        'status',
        'payment_date',
        'transaction_id',
        'academic_year',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'total_amount_due' => 'decimal:2',
        'payment_date' => 'date',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
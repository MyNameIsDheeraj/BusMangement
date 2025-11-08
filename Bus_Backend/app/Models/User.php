<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function parent()
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }
    
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class, 'user_id');
    }
    
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }
    
    public function classTeacher()
    {
        return $this->hasMany(ClassModel::class, 'class_teacher_id');
    }
    
    public function busesAsDriver()
    {
        return $this->hasMany(Bus::class, 'driver_id');
    }
    
    public function busesAsCleaner()
    {
        return $this->hasMany(Bus::class, 'cleaner_id');
    }
    
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }
    
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }
    
    public function alerts()
    {
        return $this->hasMany(Alert::class, 'submitted_by');
    }
    
    public function markedAttendances()
    {
        return $this->hasMany(BusAttendance::class, 'marked_by');
    }
}
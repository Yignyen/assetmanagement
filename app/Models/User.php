<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Asset;
use App\Models\Location;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'location_id',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($user) {

            Asset::where('assigned_to', $user->id)
                ->where('assigned_type', self::class)
                ->update([
                    'assigned_to'   => null,
                    'assigned_type' => null,
                    'location_id'   => null,
                    'status'        => 'available',
                ]);
        });
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

     public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to')
            ->where('assigned_type', self::class);
    }
}

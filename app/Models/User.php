<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'role',
        'phone',
        'password',
    ];

    protected $hidden = ['password'];

    public function carProfile()
    {
        return $this->hasOne(CarProfile::class);
    }

    public function sparePartsProfile()
    {
        return $this->hasOne(SparePartsProfile::class);
    }

    public function towTruckProfile()
    {
        return $this->hasOne(TowTruckProfile::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function spareParts()
    {
        return $this->hasMany(SparePart::class);
    }

    /**
     * Get all of the spare part posts for the user.
     */
    public function sparePartPosts()
    {
        return $this->hasMany(SparePartPost::class);
    }
}

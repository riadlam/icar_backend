<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartsProfile extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'mobile',
        'city',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

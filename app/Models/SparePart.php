<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'description',
        'supported_brands',
        'additional_phone',
    ];

    protected $casts = [
        'supported_brands' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

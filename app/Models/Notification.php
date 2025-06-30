<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array', // Automatically de/encodes JSON for the 'data' attribute
        'read_at' => 'datetime', // Ensures read_at is treated as a Carbon instance
    ];
}

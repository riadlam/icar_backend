<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalPhone extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone_number',
    ];

    /**
     * Get the user that owns the additional phone.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

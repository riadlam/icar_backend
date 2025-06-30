<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarageProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'mechanic_name',
        'mobile',
        'city',
        'services',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    /**
     * Get the user that owns the garage profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

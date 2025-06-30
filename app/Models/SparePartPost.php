<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparePartPost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spare_parts_posts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'spare_parts_category',
        'spare_parts_subcategory'
    ];

    /**
     * Get the user that owns the spare part post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

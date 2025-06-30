<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'brand',
        'model',
        'price',
        'mileage',
        'year',
        'transmission',
        'fuel',
        'description',
        'images',
        'enabled',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'float',
        'mileage' => 'integer',
        'year' => 'integer',
        'enabled' => 'boolean',
    ];

    protected $hidden = ['image_urls'];

    protected static function booted()
    {
        static::retrieved(function ($car) {
            // Ensure images are always an array with full URLs
            if (is_string($car->images)) {
                $car->images = json_decode($car->images, true) ?? [];
            }
            
            // Convert relative paths to full URLs
            if (is_array($car->images)) {
                $car->images = array_map(function($path) {
                    if (empty($path)) return $path;
                    if (strpos($path, 'http') === 0) return $path;
                    return asset('storage/' . ltrim(str_replace('storage/', '', $path), '/'));
                }, $car->images);
            } else {
                $car->images = [];
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

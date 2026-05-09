<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'emoji', // Keep for backward compatibility
        'logo_path',
        'category_id',
        'short_description',
        'full_description',
        'warranty',
        'terms_conditions',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the minimum price from variants or return null.
     */
    public function getMinPriceAttribute()
    {
        if ($this->variants->isEmpty()) {
            return null;
        }
        
        return $this->variants->min('price');
    }

    /**
     * Get the logo URL or emoji as fallback.
     */
    public function getLogoAttribute()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return $this->emoji ?? '📦';
    }
}

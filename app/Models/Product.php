<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'base_price',
        'sale_price',
        'stock_quantity',
        'sku',
        'images',
        'thumbnail',
        'status',
        'ban_reason',
        'is_featured',
        'weight',
        'tags',
        'avg_rating',
        'review_count',
        'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'tags' => 'array',
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variantOptions(): HasMany
    {
        return $this->hasMany(ProductVariantOption::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->base_price;
    }

    public function scopeActive(Product | User $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Product | User $query)
    {
        return $query->where('is_featured', true);
    }

    public function updateRating(): void
    {
        $reviews = $this->reviews()->where('is_approved', true);
        $this->update([
            'avg_rating' => $reviews->avg('rating') ?? 0,
            'review_count' => $reviews->count(),
        ]);
    }
}

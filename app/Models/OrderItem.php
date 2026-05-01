<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'variant_id',
        'product_name',
        'variant_label',
        'product_image',
        'quantity',
        'unit_price',
        'subtotal',
        'vendor_earnings',
        'fulfillment_status',
        'review_eligible',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'vendor_earnings' => 'decimal:2',
            'review_eligible' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function hasReview(): bool
    {
        return $this->review()->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }

    // Merge guest cart into user cart on login
    public static function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestCart = static::where('session_id', $sessionId)->first();
        if (!$guestCart) return;

        $userCart = static::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'variant_options' => $item->variant_options,
                ]);
            }
        }

        $guestCart->delete();
    }
}

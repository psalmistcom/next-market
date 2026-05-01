<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::with(['items.product', 'items.variant'])
                ->firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = session()->getId();
        return Cart::with(['items.product', 'items.variant'])
            ->firstOrCreate(['session_id' => $sessionId]);
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getCart();

        $product = Product::findOrFail($productId);
        $price = $product->effective_price;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $price = $variant->effective_price;
            $variantOptions = $variant->options;
        } else {
            $variantOptions = null;
        }

        $existing = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $price,
            'variant_options' => $variantOptions,
        ]);
    }

    public function updateItem(int $cartItemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    public function removeItem(int $cartItemId): void
    {
        $cart = $this->getCart();
        $cart->items()->findOrFail($cartItemId)->delete();
    }

    public function clearCart(): void
    {
        $this->getCart()->items()->delete();
    }

    public function getCartData(): array
    {
        $cart = $this->getCart();
        $items = $cart->items->map(fn($item) => [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'price' => (float) $item->price,
            'subtotal' => (float) $item->subtotal,
            'variant_options' => $item->variant_options,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'thumbnail' => $item->product->thumbnail,
                'slug' => $item->product->slug,
            ],
            'variant' => $item->variant ? [
                'id' => $item->variant->id,
                'label' => $item->variant->label,
            ] : null,
        ]);

        return [
            'items' => $items,
            'item_count' => $cart->item_count,
            'total' => $cart->total,
        ];
    }
}

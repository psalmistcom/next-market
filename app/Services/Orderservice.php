<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(array $shippingData, Cart $cart): Order
    {
        return DB::transaction(function () use ($shippingData, $cart) {
            $items = $cart->items()->with(['product.vendor.vendorProfile', 'variant'])->get();

            $subtotal = $items->sum('subtotal');
            $shippingCost = $this->calculateShipping($items);
            $taxAmount = round($subtotal * 0.075, 2); // 7.5% VAT
            $total = $subtotal + $shippingCost + $taxAmount;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total' => $total,
                'payment_status' => 'pending',
                ...$shippingData,
            ]);

            foreach ($items as $item) {
                $vendorProfile = $item->product->vendor->vendorProfile;
                $commissionRate = $vendorProfile?->commission_rate ?? 10;
                $vendorEarnings = $item->subtotal * (1 - $commissionRate / 100);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->vendor_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_label' => $item->variant?->label,
                    'product_image' => $item->product->thumbnail,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'subtotal' => $item->subtotal,
                    'vendor_earnings' => $vendorEarnings,
                    'fulfillment_status' => 'pending',
                    'review_eligible' => false,
                ]);

                // Decrement stock
                $item->product->decrement('stock_quantity', $item->quantity);
                if ($item->variant) {
                    $item->variant->decrement('stock_quantity', $item->quantity);
                }

                // Update vendor earnings
                if ($vendorProfile) {
                    $vendorProfile->increment('pending_payout', $vendorEarnings);
                }
            }

            // Clear the cart
            $cart->items()->delete();

            return $order->load('items');
        });
    }

    private function calculateShipping($items): float
    {
        // Simple flat rate: free over 50, else 5
        $subtotal = $items->sum('subtotal');
        return $subtotal >= 50 ? 0 : 5.00;
    }
}

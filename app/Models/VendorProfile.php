<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VendorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'logo',
        'banner',
        'phone',
        'address',
        'city',
        'country',
        'status',
        'suspension_reason',
        'commission_rate',
        'total_earnings',
        'pending_payout',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'pending_payout' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($profile) {
            if (empty($profile->store_slug)) {
                $profile->store_slug = Str::slug($profile->store_name);
            }
        });
    }
}

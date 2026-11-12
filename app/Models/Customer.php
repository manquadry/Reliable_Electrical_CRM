<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'customer_code',
        'contact_person',
        'phone',
        'alt_phone',
        'email',
        'category',
        'territory',
        'address',
        'city',
        'state',
        'landmark',
        'latitude',
        'longitude',
        'credit_limit',
        'payment_terms',
        'onboarded_by',
        'onboarded_date',
        'verification_status',
        'status',
        'storefront_image',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'onboarded_date' => 'date',
            'credit_limit' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function onboardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'onboarded_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(FieldActivity::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'retailer' => 'Retail Shop',
            'wholesaler' => 'Wholesale Distributor',
            'electrical_contractor' => 'Electrical Contractor',
            'lighting_showroom' => 'Lighting Showroom',
            'supermarket' => 'Supermarket / Chain',
            'real_estate_developer' => 'Estate Developer',
            'walk_in' => 'Walk-In / Counter Sales',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }

    public function isWalkIn(): bool
    {
        return $this->category === 'walk_in' || $this->customer_code === 'CUST-WALKIN-HQ';
    }

    public static function getWalkInCustomer(): self
    {
        $adminOrManagerId = User::whereIn('role', ['sales_manager', 'admin'])->orderBy('id', 'desc')->value('id') ?? 1;

        return static::firstOrCreate(
            ['customer_code' => 'CUST-WALKIN-HQ'],
            [
                'company_name' => 'Walk-in Customer (Gbagada Showroom Counter)',
                'contact_person' => 'Direct Walk-in Buyer',
                'phone' => '+234 803 555 0192',
                'alt_phone' => null,
                'email' => 'counter@reliableelectrics.ng',
                'category' => 'walk_in',
                'territory' => 'Gbagada HQ & Direct Counter',
                'address' => 'Phase 1, 32B Oguntona Crescent, Gbagada Showroom Counter',
                'city' => 'Lagos',
                'state' => 'Lagos State',
                'landmark' => 'Reliable Electrics Central Warehouse',
                'latitude' => 6.556800,
                'longitude' => 3.391200,
                'credit_limit' => 0.00,
                'payment_terms' => 'cash_on_delivery',
                'onboarded_by' => $adminOrManagerId,
                'onboarded_date' => now()->toDateString(),
                'verification_status' => 'verified',
                'status' => 'active',
                'notes' => 'Dedicated master customer account for direct over-the-counter cash, POS terminal, and instant transfer walk-in buyers.',
            ]
        );
    }

    public function getTotalOrdersAttribute(): float
    {
        return (float) $this->orders()->sum('total_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'confirmed')->sum('amount');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return max(0, $this->total_orders - $this->total_paid);
    }
}


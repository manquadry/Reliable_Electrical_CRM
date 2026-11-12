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
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
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


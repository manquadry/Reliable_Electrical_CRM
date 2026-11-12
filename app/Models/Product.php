<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'description',
        'unit_price',
        'wholesale_price',
        'minimum_order_quantity',
        'stock_quantity',
        'unit_measure',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'minimum_order_quantity' => 'integer',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'lighting' => 'Lighting & Fixtures',
            'switchgear_protection' => 'Switchgear & Protection',
            'cables_wiring' => 'Cables & Wiring',
            'solar_power' => 'Solar & Inverters',
            'home_appliances' => 'Home Appliances',
            'switches_sockets' => 'Switches & Sockets',
            default => ucfirst(str_replace('_', ' ', $this->category)),
        };
    }
}


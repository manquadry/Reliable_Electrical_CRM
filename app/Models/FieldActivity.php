<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'activity_date',
        'check_in_time',
        'check_out_time',
        'activity_type',
        'purpose',
        'notes',
        'outcome',
        'next_followup_date',
        'latitude',
        'longitude',
        'location_address',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'next_followup_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getActivityTypeLabelAttribute(): string
    {
        return match ($this->activity_type) {
            'store_visit' => 'Store Visit / Meeting',
            'new_customer_onboarding' => 'Customer Onboarding',
            'order_booking' => 'Order Booking',
            'payment_collection' => 'Payment Collection',
            'product_demo' => 'Product Demonstration',
            'complaint_resolution' => 'Complaint Resolution',
            default => ucfirst(str_replace('_', ' ', $this->activity_type)),
        };
    }

    public function getOutcomeLabelAttribute(): string
    {
        return match ($this->outcome) {
            'order_taken' => 'Order Secured',
            'payment_received' => 'Payment Collected',
            'followup_scheduled' => 'Follow-up Scheduled',
            'prospect_interested' => 'Interested Prospect',
            'not_interested' => 'Not Interested',
            'closed' => 'Resolved / Closed',
            default => ucfirst(str_replace('_', ' ', $this->outcome)),
        };
    }
}


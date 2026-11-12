<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'agent_code',
        'phone',
        'territory',
        'vehicle_type',
        'monthly_revenue_target',
        'daily_visit_target',
        'monthly_onboard_target',
        'onboarded_date',
        'onboarded_by_admin_id',
        'status',
        'avatar',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarded_date' => 'date',
            'monthly_revenue_target' => 'decimal:2',
            'daily_visit_target' => 'integer',
            'monthly_onboard_target' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFieldAgent(): bool
    {
        return $this->role === 'field_agent';
    }

    public function isSalesManager(): bool
    {
        return in_array($this->role, ['sales_manager', 'lead', 'hod']);
    }

    public function isLeadOrAdmin(): bool
    {
        return $this->isAdmin() || $this->isSalesManager();
    }

    public function isManagerOrAdmin(): bool
    {
        return $this->isAdmin() || $this->isSalesManager();
    }

    public function canManageAgents(): bool
    {
        return $this->isLeadOrAdmin();
    }

    public function canViewReports(): bool
    {
        return $this->isLeadOrAdmin();
    }

    public function canApproveOrders(): bool
    {
        return $this->isLeadOrAdmin();
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Executive Administrator',
            'sales_manager', 'lead', 'hod' => 'Field Sales Lead (HOD)',
            default => 'Field Sales Representative',
        };
    }

    public function onboardedCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'onboarded_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(FieldActivity::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function onboardedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'onboarded_by_admin_id');
    }

    public function scopeFieldAgents(Builder $query): Builder
    {
        return $query->where('role', 'field_agent');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}

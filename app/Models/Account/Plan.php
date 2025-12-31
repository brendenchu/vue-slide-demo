<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Plan model for managing subscription plans.
 *
 * Plans define the features, pricing, and limits for subscriptions.
 * They can be active or inactive, and support trial periods.
 */
class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'account_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'interval',
        'trial_period',
        'trial_interval',
        'is_active',
        'features',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'trial_period' => 'integer',
            'is_active' => 'boolean',
            'features' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get only active subscriptions for this plan.
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->where('status', 'active')
            ->whereNull('canceled_at');
    }

    /**
     * Scope to only include active plans.
     *
     * @param  Builder  $query  The query builder instance
     * @return Builder Modified query builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter plans by interval (monthly, yearly, etc.).
     *
     * @param  Builder  $query  The query builder instance
     * @param  string  $interval  The billing interval
     * @return Builder Modified query builder
     */
    public function scopeInterval(Builder $query, string $interval): Builder
    {
        return $query->where('interval', $interval);
    }

    /**
     * Check if the plan offers a trial period.
     *
     * @return bool True if trial period is available
     */
    public function hasTrial(): bool
    {
        return $this->trial_period > 0;
    }

    /**
     * Check if the plan is currently active.
     *
     * @return bool True if plan is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Get the plan's price formatted as currency.
     *
     * @param  string  $currency  Currency symbol (default: $)
     * @return string Formatted price
     */
    public function formattedPrice(string $currency = '$'): string
    {
        return $currency . number_format($this->price, 2);
    }
}

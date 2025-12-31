<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Subscription model for managing account subscriptions.
 *
 * Subscriptions are polymorphic and can belong to different accountable entities
 * (e.g., User, Team). Each subscription is tied to a specific Plan.
 */
class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'account_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'accountable_id',
        'accountable_type',
        'plan_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'canceled_by',
        'cancellation_reason',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the owning accountable model (User, Team, etc.).
     *
     * This is a polymorphic relationship allowing subscriptions to belong
     * to different types of entities.
     */
    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the subscription's plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if the subscription is currently active.
     *
     * @return bool True if subscription is active and not canceled
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->ends_at?->isFuture()
            && is_null($this->canceled_at);
    }

    /**
     * Check if the subscription is in trial period.
     *
     * @return bool True if currently in trial
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at !== null
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the subscription has been canceled.
     *
     * @return bool True if subscription is canceled
     */
    public function isCanceled(): bool
    {
        return $this->canceled_at !== null;
    }
}

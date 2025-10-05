<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ends_at'
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function willCancelAtPeriodEnd(): bool
    {
        return $this->isActive() && $this->canceled_at !== null;
    }
}
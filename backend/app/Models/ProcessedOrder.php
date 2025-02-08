<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $subscription_id
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription $subscription
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProcessedOrder whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class ProcessedOrder extends Model
{
    protected $fillable = [
        'subscription_id',
        'order_id',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }
}

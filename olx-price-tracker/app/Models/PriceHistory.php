<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PriceHistory
 * @property int $id
 * @property float $price
 * @property int $subscription_id
 * @property string $currency
 * ---------
 * @property PriceHistory $subscription
 */
class PriceHistory extends Model
{
    use HasFactory;
    protected $fillable = ['price', 'subscription_id', 'currency'];

    protected $casts = [
        'price' => 'float'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

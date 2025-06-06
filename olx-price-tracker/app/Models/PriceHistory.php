<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PriceHistory
 * @property int $id
 * @property float $price
 * @property int $subscription_id
 * @property string $currency
 * @property Carbon $date
 * ---------
 * @property PriceHistory $subscription
 */
class PriceHistory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['price', 'subscription_id', 'currency', 'date'];

    protected $casts = [
        'price' => 'float',
        'date' => 'datetime'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

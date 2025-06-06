<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Subscription
 * @property int $id
 * @property string $url
 * @property string $email
 * @property float $current_price
 * @property string $current_currency
 * @property bool $is_active
 * @property string $token
 * @property Carbon $date
 * ---------
 * @property Collection<PriceHistory> $priceHistories
 */
class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'email',
        'current_price',
        'current_currency',
        'is_active',
        'token',
        'date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_price' => 'float',
        'date' => 'datetime'
    ];

    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}

<?php

namespace Database\Factories;

use App\Models\PriceHistory;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceHistoryFactory extends Factory
{
    protected $model = PriceHistory::class;

    public function definition()
    {
        return [
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->currencyCode,
            'subscription_id' => Subscription::factory(),
        ];
    }
}

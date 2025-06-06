<?php

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'email' => $this->faker->email,
            'current_price' => $this->faker->randomFloat(2, 1, 100),
            'current_currency' => 'USD',
            'is_active' => false,
            'token' => $this->faker->uuid,
        ];
    }
}

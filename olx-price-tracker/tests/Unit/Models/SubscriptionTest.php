<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_subscription()
    {
        $now = Carbon::now()->startOfSecond();
        /** @var Subscription $subscription */
        $subscription = Subscription::factory()->create([
            'url' => 'https://example.com',
            'email' => 'test@example.com',
            'current_price' => 100.50,
            'current_currency' => 'USD',
            'is_active' => true,
            'date' => $now,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'email' => 'test@example.com',
            'current_price' => 100.50,
            'date' => $now->toDateTimeString(),
        ]);

        $this->assertInstanceOf(Carbon::class, $subscription->date);
        $this->assertTrue($subscription->date->equalTo($now));
    }

    #[Test]
    public function it_has_price_histories_relation()
    {
        $subscription = Subscription::factory()
            ->has(PriceHistory::factory()->count(3))
            ->create();

        $this->assertInstanceOf(PriceHistory::class, $subscription->priceHistories->first());
        $this->assertCount(3, $subscription->priceHistories);
    }

    #[Test]
    public function it_casts_is_active_to_boolean()
    {
        $subscription = Subscription::factory()->create(['is_active' => 1]);
        $this->assertTrue($subscription->is_active);

        $subscription->update(['is_active' => 0]);
        $this->assertFalse($subscription->fresh()->is_active);
    }

    #[Test]
    public function it_casts_current_price_to_float()
    {
        $subscription = Subscription::factory()->create(['current_price' => '150.75']);
        $this->assertSame(150.75, $subscription->current_price);
        $this->assertIsFloat($subscription->current_price);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PriceHistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_price_history()
    {
        $now = Carbon::now()->startOfSecond();
        /** @var Subscription $subscription */
        $subscription = Subscription::factory()->create();
        /** @var PriceHistory $history */
        $history = PriceHistory::factory()->create([
            'subscription_id' => $subscription->id,
            'price' => 99.99,
            'currency' => 'EUR',
            'date' => $now,
        ]);

        $this->assertDatabaseHas('price_histories', [
            'id' => $history->id,
            'price' => 99.99,
            'currency' => 'EUR',
            'date' => $now->toDateTimeString(),
        ]);

        $this->assertInstanceOf(Carbon::class, $history->date);
        $this->assertTrue($history->date->equalTo($now));
    }

    #[Test]
    public function it_belongs_to_subscription()
    {
        /** @var Subscription $subscription */
        $subscription = Subscription::factory()->create();
        /** @var PriceHistory $history */
        $history = PriceHistory::factory()->create(['subscription_id' => $subscription->id]);

        $this->assertInstanceOf(Subscription::class, $history->subscription);
        $this->assertEquals($subscription->id, $history->subscription->id);
    }

    #[Test]
    public function it_casts_price_to_float()
    {
        /** @var PriceHistory $history */
        $history = PriceHistory::factory()->create(['price' => '200.50']);
        $this->assertSame(200.50, $history->price);
        $this->assertIsFloat($history->price);
    }
}

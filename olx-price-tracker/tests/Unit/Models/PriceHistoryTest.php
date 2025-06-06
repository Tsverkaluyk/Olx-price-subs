<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceHistoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_price_history()
    {
        $subscription = Subscription::factory()->create();
        $history = PriceHistory::factory()->create([
            'subscription_id' => $subscription->id,
            'price' => 99.99,
            'currency' => 'EUR',
        ]);

        $this->assertDatabaseHas('price_histories', [
            'id' => $history->id,
            'price' => 99.99,
            'currency' => 'EUR',
        ]);
    }

    /** @test */
    public function it_belongs_to_subscription()
    {
        $subscription = Subscription::factory()->create();
        $history = PriceHistory::factory()->create(['subscription_id' => $subscription->id]);

        $this->assertInstanceOf(Subscription::class, $history->subscription);
        $this->assertEquals($subscription->id, $history->subscription->id);
    }

    /** @test */
    public function it_casts_price_to_float()
    {
        $history = PriceHistory::factory()->create(['price' => '200.50']);
        $this->assertSame(200.50, $history->price);
        $this->assertIsFloat($history->price);
    }
}

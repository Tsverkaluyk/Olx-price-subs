<?php

namespace Tests\Unit\Jobs;

use App\Enums\NotificationType;
use App\Jobs\CheckOlxPricesJob;
use App\Mail\SubcribeNotify;
use App\Models\Subscription;
use App\Services\OlxParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckOlxPricesJobTest extends TestCase
{
    use RefreshDatabase;

    private OlxParser $parser;
    private CheckOlxPricesJob $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = $this->createMock(OlxParser::class);
        $this->job = new CheckOlxPricesJob();
    }

    #[Test]
    public function updates_price_and_sends_notification_when_price_changes()
    {
        Mail::fake();

        $subscription = Subscription::factory()->create([
            'current_price' => 1000,
            'current_currency' => 'UAH',
            'is_active' => true
        ]);

        $newPriceData = ['price' => 1200, 'currency' => 'UAH'];

        $this->parser->method('getPrice')
            ->willReturn($newPriceData);

        $this->job->handle($this->parser);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'current_price' => 1200
        ]);

        $this->assertDatabaseHas('price_histories', [
            'subscription_id' => $subscription->id,
            'price' => 1200
        ]);

        Mail::assertQueued(SubcribeNotify::class, function ($mail) use ($subscription) {
            return $mail->hasTo($subscription->email) &&
                $mail->type === NotificationType::PRICE_CHANGE;
        });
    }

    #[Test]
    public function does_nothing_when_price_remains_the_same()
    {
        Mail::fake();

        $subscription = Subscription::factory()->create([
            'current_price' => 1000,
            'current_currency' => 'UAH',
            'is_active' => true
        ]);

        $samePriceData = ['price' => 1000, 'currency' => 'UAH'];

        $this->parser->method('getPrice')
            ->willReturn($samePriceData);

        $this->job->handle($this->parser);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'current_price' => 1000
        ]);

        $this->assertDatabaseCount('price_histories', 0);
        Mail::assertNotQueued(SubcribeNotify::class);
    }

    #[Test]
    public function handles_currency_change()
    {
        Mail::fake();

        $subscription = Subscription::factory()->create([
            'current_price' => 1000,
            'current_currency' => 'UAH',
            'is_active' => true
        ]);

        $newCurrencyData = ['price' => 50, 'currency' => 'USD'];

        $this->parser->method('getPrice')
            ->willReturn($newCurrencyData);

        $this->job->handle($this->parser);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'current_price' => 50,
            'current_currency' => 'USD'
        ]);

        Mail::assertQueued(SubcribeNotify::class);
    }

    #[Test]
    public function ignores_inactive_subscriptions()
    {
        Mail::fake();

        $subscription = Subscription::factory()->create([
            'current_price' => 1000,
            'is_active' => false
        ]);

        $this->parser->expects($this->never())
            ->method('getPrice');

        $this->job->handle($this->parser);

        Mail::assertNotQueued(SubcribeNotify::class);
    }

    #[Test]
    public function handles_chunking_correctly()
    {
        Mail::fake();

        Subscription::factory()->count(250)->create(['is_active' => true]);

        $this->parser->expects($this->exactly(250))
            ->method('getPrice')
            ->willReturn(['price' => 1500, 'currency' => 'UAH']);

        $this->job->handle($this->parser);

        $this->assertDatabaseCount('price_histories', 250);
    }
}

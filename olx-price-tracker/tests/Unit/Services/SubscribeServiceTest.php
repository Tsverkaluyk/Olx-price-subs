<?php

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Services\SubscribeService;
use App\Services\OlxParser;
use App\Mail\SubcribeNotify;
use App\Enums\NotificationType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SubscribeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_creates_new_subscription_and_sends_mail()
    {
        Mail::fake();

        $parserMock = $this->createMock(OlxParser::class);
        $parserMock->method('getPrice')->willReturn([
            'price' => 1000,
            'currency' => 'UAH',
        ]);

        $service = new SubscribeService();

        $data = [
            'url' => 'https://www.olx.ua/test',
            'email' => 'test@example.com',
        ];

        $subscription = $service->subscribe($data, $parserMock);

        $this->assertDatabaseHas('subscriptions', [
            'email' => $data['email'],
            'url' => $data['url'],
            'is_active' => true,
            'current_price' => 1000,
            'current_currency' => 'UAH',
        ]);
        Mail::assertSent(SubcribeNotify::class);
        $this->assertNotNull($subscription->token);
        $this->assertTrue($subscription->is_active);
    }

    public function test_subscribe_throws_exception_when_price_not_found()
    {
        $this->expectException(HttpException::class);

        $parserMock = $this->createMock(OlxParser::class);
        $parserMock->method('getPrice')->willReturn(null);

        $service = new SubscribeService();

        $data = [
            'url' => 'https://www.olx.ua/test',
            'email' => 'test@example.com',
        ];

        $service->subscribe($data, $parserMock);
    }

    public function test_unsubscribe_returns_false_if_token_not_found()
    {
        $service = new SubscribeService();

        $result = $service->unsubscribe('non-existing-token');

        $this->assertFalse($result);
    }

    public function test_unsubscribe_deactivates_subscription_and_returns_true()
    {
        $token = Str::uuid();
        $subscription = Subscription::factory()->create([
            'is_active' => true,
            'token' => $token,
        ]);

        $service = new SubscribeService();

        $result = $service->unsubscribe($token);

        $this->assertTrue($result);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'is_active' => false,
        ]);
    }
}

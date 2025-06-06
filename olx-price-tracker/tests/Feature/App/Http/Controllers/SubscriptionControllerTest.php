<?php

namespace Tests\Feature;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubcribeNotify;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_subscribe_successfully()
    {
        Mail::fake();

        $response = $this->postJson('/api/subscribe', [
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/utsi-komplekt-dlya-tokarnogo-frezernogo-IDXhpGb.html',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/utsi-komplekt-dlya-tokarnogo-frezernogo-IDXhpGb.html',
            'is_active' => true
        ]);

        Mail::assertSent(SubcribeNotify::class);
    }

    public function test_invalid_url_returns_error()
    {
        $response = $this->postJson('/api/subscribe', [
            'url' => 'https://not-olx.com/item',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    public function test_invalid_mail_returns_error()
    {
        $response = $this->postJson('/api/subscribe', [
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/utsi-komplekt-dlya-tokarnogo-frezernogo-IDXhpGb.html',
            'email' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_invalid_mail_url_returns_error()
    {
        $response = $this->postJson('/api/subscribe', [
            'url' => 'test',
            'email' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'url']);
    }

    public function test_can_unsubscribe()
    {
        $subscription = Subscription::factory()->create([
            'is_active' => true,
            'token' => 'test-token',
        ]);

        $response = $this->getJson('/api/unsubscribe/test-token');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Ви успішно відписалися',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'is_active' => false
        ]);
    }

    public function test_invalid_unsubscribe()
    {
        $subscription = Subscription::factory()->create([
            'is_active' => true,
            'token' => 'test-token',
        ]);

        $response = $this->getJson('/api/unsubscribe/invalid-token');

        $response->assertStatus(404);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'is_active' => true,
        ]);
    }

}

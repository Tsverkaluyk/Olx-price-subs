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
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/frontalniy-navantazhuvach-lonking-cdm936n-IDT23ql.html?reason=hp%7Cpromoted',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/frontalniy-navantazhuvach-lonking-cdm936n-IDT23ql.html?reason=hp%7Cpromoted',
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
            'url' => 'https://www.olx.ua/d/uk/obyavlenie/frontalniy-navantazhuvach-lonking-cdm936n-IDT23ql.html?reason=hp%7Cpromoted',
            'email' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['mail']);
    }

    public function test_invalid_mail_url_returns_error()
    {
        $response = $this->postJson('/api/subscribe', [
            'url' => 'test',
            'email' => 'test',
        ]);

        $response->assertStatus(422); // validation error
        $response->assertJsonValidationErrors(['mail', 'url']);
    }

    public function test_can_unsubscribe()
    {
        $subscription = Subscription::factory()->create([
            'is_active' => true,
            'token' => 'test-token',
        ]);

        $response = $this->postJson('/api/unsubscribe/test-token');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Ви успішно відписалися',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'is_active' => false
        ]);
    }
}

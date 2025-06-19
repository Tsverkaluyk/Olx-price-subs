<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Mail\SubcribeNotify;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubscribeService
{
    public function subscribe(array $validated, SiteParser $parser): Subscription
    {
        $subscription = Subscription::firstOrNew([
            'url' => $validated['url'],
            'email' => $validated['email']
        ]);

        if (!$subscription->exists || !$subscription->is_active) {
            $currentPrice = $parser->getPrice($validated['url']);

            if (!$currentPrice) {
                throw new HttpException(400, 'Не вдалося отримати ціну з цього URL');
            }

            $subscription->fill([
                'current_price' => $currentPrice['price'],
                'current_currency' => $currentPrice['currency'],
                'is_active' => true,
                'token' => Str::uuid(),
                'date' => Carbon::now()
            ])->save();

            Mail::to($validated['email'])->send(
                new SubcribeNotify($subscription, NotificationType::SUBSCRIPTION)
            );
        }

        return $subscription;
    }

    public function unsubscribe(string $token): bool
    {
        $subscription = Subscription::where('token', $token)->first();

        if (!$subscription) {
            return false;
        }

        $subscription->update(['is_active' => false]);

        return true;
    }
}

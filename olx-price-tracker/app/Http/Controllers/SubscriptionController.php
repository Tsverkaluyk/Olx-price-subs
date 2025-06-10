<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Http\Requests\SubscribeRequest;
use App\Mail\SubcribeNotify;
use App\Models\Subscription;
use App\Services\OlxParser;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SubscriptionController
{
    /**
     * @param  SubscribeRequest  $request
     * @param  OlxParser  $parser
     * @return JsonResponse
     */
    public function subscribe(SubscribeRequest $request, OlxParser $parser): JsonResponse
    {
        $validated = $request->validated();
        $subscription = Subscription::firstOrNew([
            'url' => $validated['url'],
            'email' => $validated['email']
        ]);

        if (!$subscription->exists || !$subscription->is_active) {
            $currentPrice = $parser->getPrice($validated['url']);

            if (!$currentPrice) {
                return response()->json([
                    'error' => 'Не вдалося отримати ціну з цього URL'
                ], 400);
            }

            $subscription->fill([
                'current_price' => $currentPrice['price'],
                'current_currency' => $currentPrice['currency'],
                'is_active' => true,
                'token' => Str::random(32),
                'date' => Carbon::now()
            ])->save();

            Mail::to($validated['email'])->send(
                new SubcribeNotify($subscription, NotificationType::SUBSCRIPTION)
            );
        }

        return response()->json([
            'message' => 'Підписка успішно створена або оновлена',
            'token' => $subscription->token
        ]);
    }

    /**
     * @param $token
     * @return JsonResponse
     */
    public function unsubscribe($token): JsonResponse
    {
        $subscription = Subscription::where('token', $token)->firstOrFail();
        $subscription->update(['is_active' => false]);

        return response()->json(['message' => 'Ви успішно відписалися']);
    }
}

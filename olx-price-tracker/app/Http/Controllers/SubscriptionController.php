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
     * @OA\Post(
     *     path="/api/subscribe",
     *     summary="Підписка на оголошення OLX",
     *     tags={"Subscription"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SubscribeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Підписка створена або оновлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Підписка успішно створена або оновлена"),
     *             @OA\Property(property="token", type="string", example="f8a3b9e540c841aaac2c6a5f9d99a1df")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Помилка при отриманні ціни",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Не вдалося отримати ціну з цього URL")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/unsubscribe/{token}",
     *     summary="Відписка від сповіщень",
     *     tags={"Subscription"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Унікальний токен підписки",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Відписка успішна",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ви успішно відписалися")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Підписка не знайдена"
     *     )
     * )
     */

    public function unsubscribe($token): JsonResponse
    {
        $subscription = Subscription::where('token', $token)->firstOrFail();
        $subscription->update(['is_active' => false]);

        return response()->json(['message' => 'Ви успішно відписалися']);
    }
}

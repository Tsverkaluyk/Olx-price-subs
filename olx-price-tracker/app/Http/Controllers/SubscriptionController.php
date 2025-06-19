<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Http\Resources\SubscriptionResource;
use App\Services\OlxParser;
use App\Services\SubscribeService;
use Illuminate\Http\JsonResponse;

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
     *         description="Підписка успішно створена або оновлена",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Subscription"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Помилка при отриманні ціни",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Не вдалося отримати ціну з цього URL"
     *             )
     *         )
     *     )
     * )
     */
    public function subscribe(
        SubscribeRequest $request,
        OlxParser $parser,
        SubscribeService $service
    ): SubscriptionResource {
        $validated = $request->validated();
        $subscription = $service->subscribe($validated, $parser);

        return SubscriptionResource::make($subscription);
    }


    /**
     * @OA\Delete(
     *     path="/api/unsubscribe/{token}",
     *     summary="Відписатися від підписки за токеном",
     *     tags={"Subscription"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="Унікальний токен підписки",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успішна відписка",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ви успішно відписалися")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Підписка не знайдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Підписка не знайдена")
     *         )
     *     )
     * )
     */
    public function unsubscribe(string $token, SubscribeService $service): JsonResponse
    {
        $result = $service->unsubscribe($token);

        if (!$result) {
            return response()->json(['error' => 'Підписка не знайдена'], 404);
        }

        return response()->json(['message' => 'Ви успішно відписалися'], 200);
    }
}

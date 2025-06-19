<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subscription
 */

/**
 * @OA\Schema(
 *     schema="Subscription",
 *     type="object",
 *     required={"url", "email", "current_price", "currency", "is_active", "token", "date"},
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         format="url",
 *         example="https://www.olx.ua/d/uk/obyavlenie/smartfon"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="user@example.com"
 *     ),
 *     @OA\Property(
 *         property="current_price",
 *         type="number",
 *         example=2500
 *     ),
 *     @OA\Property(
 *         property="currency",
 *         type="string",
 *         example="UAH"
 *     ),
 *     @OA\Property(
 *         property="is_active",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="token",
 *         type="string",
 *         format="uuid",
 *         example="23f9d7dc-0c7f-4a20-97aa-7f23c244b159"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date-time",
 *         example="2025-06-19 13:45:00"
 *     )
 * )
 */
class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->url,
            'email' => $this->email,
            'current_price' => $this->current_price,
            'currency' => $this->current_currency,
            'is_active' => $this->is_active,
            'token' => $this->token,
            'date' => $this->date?->toDateTimeString(),
        ];
    }
}

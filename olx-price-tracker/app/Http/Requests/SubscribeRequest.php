<?php

namespace App\Http\Requests;

use App\Rules\OlxUrl;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="SubscribeRequest",
 *     required={"url", "email"},
 *     @OA\Property(property="url", type="string", format="uri", example="https://www.olx.ua/d/uk/obyavlenie/iphone-12/"),
 *     @OA\Property(property="email", type="string", format="email", example="test@example.com")
 * )
 */
class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                'starts_with:https://www.olx.ua,https://olx.ua',
                new OlxUrl()
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ];
    }
}

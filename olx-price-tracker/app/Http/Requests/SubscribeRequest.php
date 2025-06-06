<?php

namespace App\Http\Requests;

use App\Rules\OlxUrl;
use Illuminate\Foundation\Http\FormRequest;

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

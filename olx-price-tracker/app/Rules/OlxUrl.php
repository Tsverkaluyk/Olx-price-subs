<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OlxUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^https:\/\/(www\.)?olx\.ua\/.*\/d\/[a-z0-9]+\.html$/i', $value)) {
            $fail('Невірний формат URL оголошення OLX. Приклад: https://www.olx.ua/.../d123456789.html');
        }
    }
}

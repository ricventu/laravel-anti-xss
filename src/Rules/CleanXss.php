<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CleanXss implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (app('anti-xss')->contains($value)) {
            $fail('The :attribute field contains potentially malicious content.');
        }
    }
}

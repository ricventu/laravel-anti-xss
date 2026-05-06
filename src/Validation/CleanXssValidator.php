<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss\Validation;

use Illuminate\Validation\Validator;

class CleanXssValidator
{
    /**
     * Callable used by Validator::extend('clean_xss', ...).
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @param  Validator  $validator
     */
    public static function validate($attribute, $value, $parameters, $validator): bool
    {
        if (! is_string($value)) {
            return true;
        }

        return ! app('anti-xss')->contains($value);
    }
}

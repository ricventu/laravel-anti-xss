<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class CleanXssInput extends TransformsRequest
{
    /**
     * Attributes that should not be sanitized.
     *
     * Loaded from config('anti-xss.middleware.except').
     *
     * @var array<int, string>
     */
    protected array $except = [];

    public function __construct()
    {
        $this->except = (array) config('anti-xss.middleware.except', []);
    }

    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        if (! is_string($value)) {
            return $value;
        }

        return app('anti-xss')->clean($value);
    }
}

<?php

declare(strict_types=1);

use Ricventu\LaravelAntiXss\AntiXss;

if (! function_exists('anti_xss')) {
    /**
     * Get the AntiXss service instance, or sanitize the given input.
     *
     * - anti_xss() returns the singleton service.
     * - anti_xss('<script>...</script>') returns the sanitized string.
     * - anti_xss(['a' => '<script>x</script>']) returns the sanitized array.
     */
    function anti_xss(string|array|null $input = null): mixed
    {
        /** @var AntiXss $service */
        $service = app('anti-xss');

        if ($input === null) {
            return $service;
        }

        return $service->clean($input);
    }
}

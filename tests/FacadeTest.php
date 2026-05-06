<?php

declare(strict_types=1);

use Ricventu\LaravelAntiXss\AntiXss;
use Ricventu\LaravelAntiXss\Facades\AntiXss as AntiXssFacade;

it('proxies clean() through the facade', function () {
    $cleaned = AntiXssFacade::clean('<script>alert(1)</script>safe');

    expect($cleaned)->not->toContain('<script')
        ->and($cleaned)->toContain('safe');
});

it('resolves the singleton via container alias and class name', function () {
    $a = app('anti-xss');
    $b = app(AntiXss::class);

    expect($a)->toBeInstanceOf(AntiXss::class)
        ->and($b)->toBe($a);
});

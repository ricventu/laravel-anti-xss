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

it('proxies contains() through the facade', function () {
    expect(AntiXssFacade::contains('<script>x</script>'))->toBeTrue()
        ->and(AntiXssFacade::contains('safe'))->toBeFalse();
});

it('proxies fluent setters through the facade', function () {
    $service = AntiXssFacade::setReplacement('[redacted]');

    expect($service)->toBeInstanceOf(AntiXss::class);
    expect(AntiXssFacade::clean('<script>x</script>'))->toContain('[redacted]');
});

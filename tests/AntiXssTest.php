<?php

declare(strict_types=1);

use Ricventu\LaravelAntiXss\AntiXss;
use voku\helper\AntiXSS as VokuAntiXSS;

it('strips script tags', function () {
    $service = new AntiXss;

    $cleaned = $service->clean('<script>alert(1)</script>hello');

    expect($cleaned)->not->toContain('<script')
        ->and($cleaned)->toContain('hello');
});

it('strips evil event handlers', function () {
    $service = new AntiXss;

    $cleaned = $service->clean('<img src="x" onerror="alert(1)">');

    expect($cleaned)->not->toContain('onerror');
});

it('cleans an array of strings', function () {
    $service = new AntiXss;

    $cleaned = $service->clean([
        'name' => 'John',
        'bio' => '<script>alert(1)</script>safe',
    ]);

    expect($cleaned)->toBeArray()
        ->and($cleaned['name'])->toBe('John')
        ->and($cleaned['bio'])->not->toContain('<script');
});

it('reports xss detection state', function () {
    $service = new AntiXss;

    expect($service->isXssFound())->toBeNull();

    $service->clean('plain text');
    expect($service->isXssFound())->toBeFalse();

    $service->clean('<script>alert(1)</script>');
    expect($service->isXssFound())->toBeTrue();
});

it('detects xss without mutating last-call state', function () {
    $service = new AntiXss;

    expect($service->contains('<script>x</script>'))->toBeTrue()
        ->and($service->contains('safe'))->toBeFalse()
        ->and($service->isXssFound())->toBeNull();
});

it('exposes the underlying voku engine', function () {
    expect((new AntiXss)->engine())->toBeInstanceOf(VokuAntiXSS::class);
});

it('applies the replacement string from config', function () {
    $service = new AntiXss(['replacement' => '[removed]']);

    $cleaned = $service->clean('<script>alert(1)</script>');

    expect($cleaned)->toContain('[removed]');
});

it('applies extra evil html tags from config', function () {
    $service = new AntiXss([
        'evil_html_tags' => [
            'add' => ['custom-tag'],
        ],
    ]);

    $cleaned = $service->clean('<custom-tag>danger</custom-tag>');

    expect($cleaned)->not->toContain('<custom-tag');
});

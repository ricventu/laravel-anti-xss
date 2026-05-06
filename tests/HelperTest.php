<?php

declare(strict_types=1);

use Ricventu\LaravelAntiXss\AntiXss;

it('returns the service instance when called with no arguments', function () {
    expect(anti_xss())->toBeInstanceOf(AntiXss::class);
});

it('cleans a string when given one', function () {
    expect(anti_xss('<script>alert(1)</script>safe'))
        ->not->toContain('<script')
        ->toContain('safe');
});

it('cleans an array when given one', function () {
    $cleaned = anti_xss([
        'a' => '<script>x</script>',
        'b' => 'plain',
    ]);

    expect($cleaned)->toBeArray()
        ->and($cleaned['a'])->not->toContain('<script')
        ->and($cleaned['b'])->toBe('plain');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('compiles the @xss directive', function () {
    $compiled = Blade::compileString('@xss($value)');

    expect($compiled)->toContain("app('anti-xss')->clean(\$value)")
        ->and($compiled)->toContain('echo e(');
});

it('renders a sanitized and escaped value', function () {
    $rendered = Blade::render('@xss($value)', [
        'value' => '<script>alert(1)</script>safe',
    ]);

    expect($rendered)
        ->not->toContain('<script')
        ->toContain('safe');
});

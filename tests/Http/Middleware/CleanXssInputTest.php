<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ricventu\LaravelAntiXss\Http\Middleware\CleanXssInput;

beforeEach(function () {
    Route::post('/_anti_xss_test', function (Request $request) {
        return response()->json($request->all());
    })->middleware(CleanXssInput::class);
});

it('sanitizes incoming string fields', function () {
    $response = $this->postJson('/_anti_xss_test', [
        'name' => '<script>alert(1)</script>John',
    ]);

    $response->assertOk();
    expect($response->json('name'))
        ->not->toContain('<script')
        ->toContain('John');
});

it('does not touch fields listed in the except list', function () {
    config()->set('anti-xss.middleware.except', ['secret']);

    Route::post('/_anti_xss_except', function (Request $request) {
        return response()->json($request->all());
    })->middleware(CleanXssInput::class);

    $response = $this->postJson('/_anti_xss_except', [
        'secret' => '<script>keep</script>',
    ]);

    $response->assertOk();
    expect($response->json('secret'))->toBe('<script>keep</script>');
});

it('leaves non-string values untouched', function () {
    $response = $this->postJson('/_anti_xss_test', [
        'count' => 42,
        'flag' => true,
    ]);

    $response->assertOk();
    expect($response->json('count'))->toBe(42)
        ->and($response->json('flag'))->toBeTrue();
});

it('skips default password fields from config', function () {
    $response = $this->postJson('/_anti_xss_test', [
        'password' => '<script>keep</script>',
        'password_confirmation' => '<script>keep</script>',
    ]);

    $response->assertOk();
    expect($response->json('password'))->toBe('<script>keep</script>')
        ->and($response->json('password_confirmation'))->toBe('<script>keep</script>');
});

it('sanitizes nested array fields recursively', function () {
    $response = $this->postJson('/_anti_xss_test', [
        'profile' => [
            'bio' => '<script>alert(1)</script>safe',
            'tags' => ['<script>tag</script>clean'],
        ],
    ]);

    $response->assertOk();
    expect($response->json('profile.bio'))
        ->not->toContain('<script')
        ->toContain('safe');
    expect($response->json('profile.tags.0'))
        ->not->toContain('<script')
        ->toContain('clean');
});

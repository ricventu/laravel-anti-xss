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

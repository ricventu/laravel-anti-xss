<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use Ricventu\LaravelAntiXss\Rules\CleanXss;

it('fails the rule object when xss is present', function () {
    $validator = Validator::make(
        ['bio' => '<script>alert(1)</script>'],
        ['bio' => ['required', new CleanXss]]
    );

    expect($validator->fails())->toBeTrue();
});

it('passes the rule object when input is safe', function () {
    $validator = Validator::make(
        ['bio' => 'A perfectly fine bio.'],
        ['bio' => ['required', new CleanXss]]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails the string-syntax clean_xss rule when xss is present', function () {
    $validator = Validator::make(
        ['bio' => '<img src=x onerror=alert(1)>'],
        ['bio' => 'required|clean_xss']
    );

    expect($validator->fails())->toBeTrue();
});

it('passes the string-syntax clean_xss rule for safe input', function () {
    $validator = Validator::make(
        ['bio' => 'hello world'],
        ['bio' => 'required|clean_xss']
    );

    expect($validator->passes())->toBeTrue();
});

it('skips the rule object when value is not a string', function () {
    $validator = Validator::make(
        ['count' => 42],
        ['count' => [new CleanXss]]
    );

    expect($validator->passes())->toBeTrue();
});

it('skips the string-syntax clean_xss rule when value is not a string', function () {
    $validator = Validator::make(
        ['count' => 42],
        ['count' => 'clean_xss']
    );

    expect($validator->passes())->toBeTrue();
});

it('uses default error message for clean_xss rule', function () {
    $validator = Validator::make(
        ['bio' => '<script>x</script>'],
        ['bio' => 'clean_xss']
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('bio'))->toContain('malicious');
});

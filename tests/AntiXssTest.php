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

it('removes evil html tags via config', function () {
    $service = new AntiXss([
        'evil_html_tags' => [
            'remove' => ['applet'],
        ],
    ]);

    $cleaned = $service->clean('<applet>x</applet>');

    expect($cleaned)->toContain('<applet');
});

it('applies extra evil attributes from config', function () {
    $service = new AntiXss([
        'evil_attributes' => [
            'add' => ['data-evil'],
        ],
    ]);

    $cleaned = $service->clean('<div data-evil="boom">x</div>');

    expect($cleaned)->not->toContain('data-evil');
});

it('removes evil attributes via config', function () {
    $service = new AntiXss([
        'evil_attributes' => [
            'remove' => ['style'],
        ],
    ]);

    $cleaned = $service->clean('<div style="color:red">x</div>');

    expect($cleaned)->toContain('style=');
});

it('honors keep_pre_and_code_tag_content config without error', function () {
    $on = new AntiXss(['keep_pre_and_code_tag_content' => true]);
    $off = new AntiXss(['keep_pre_and_code_tag_content' => false]);

    expect($on->clean('<pre>safe</pre>'))->toBeString()
        ->and($off->clean('<pre>safe</pre>'))->toBeString();
});

it('honors strip_4byte_chars config without error', function () {
    $service = new AntiXss(['strip_4byte_chars' => true]);

    $cleaned = $service->clean('hello world');

    expect($cleaned)->toContain('hello');
});

it('exposes fluent setters returning self', function () {
    $service = new AntiXss;

    $result = $service
        ->setReplacement('[X]')
        ->setKeepPreAndCodeTagContent(true)
        ->setStripe4byteChars(true)
        ->addEvilAttributes(['data-bad'])
        ->removeEvilAttributes(['style'])
        ->addEvilHtmlTags(['custom-tag'])
        ->removeEvilHtmlTags(['applet']);

    expect($result)->toBe($service);

    $cleaned = $service->clean('<script>a</script><custom-tag>b</custom-tag><div data-bad="x">c</div>');

    expect($cleaned)->toContain('[X]')
        ->and($cleaned)->not->toContain('<custom-tag')
        ->and($cleaned)->not->toContain('data-bad');
});

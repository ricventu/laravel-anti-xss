# Laravel Anti-XSS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ricventu/laravel-anti-xss.svg?style=flat-square)](https://packagist.org/packages/ricventu/laravel-anti-xss)
[![PHP Version](https://img.shields.io/packagist/php-v/ricventu/laravel-anti-xss.svg?style=flat-square)](https://packagist.org/packages/ricventu/laravel-anti-xss)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Ricventu/laravel-anti-xss/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Ricventu/laravel-anti-xss/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/Ricventu/laravel-anti-xss/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/Ricventu/laravel-anti-xss/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ricventu/laravel-anti-xss.svg?style=flat-square)](https://packagist.org/packages/ricventu/laravel-anti-xss)

A Laravel wrapper for [voku/anti-xss](https://github.com/voku/anti-xss) that provides everything you need to neutralize XSS payloads in user input:

- a singleton `AntiXss` service,
- an `AntiXss` Facade,
- a Blade directive `@xss(...)`,
- a `clean_xss` validation rule (and `CleanXss` rule object),
- an opt-in `CleanXssInput` middleware that sanitizes request input,
- a global `anti_xss()` helper.

```php
use Ricventu\LaravelAntiXss\Facades\AntiXss;

AntiXss::clean('<script>alert(1)</script>Hello');
// => 'Hello'

AntiXss::clean('<a href="javascript:alert(1)">click</a>');
// => '<a href="">click</a>'

AntiXss::clean('<img src=x onerror=alert(1)>');
// => '<img src=x>'
```

## Why this package?

- **Laravel-native.** Facade, helper, validation rule, middleware and Blade directive — drop in and use.
- **Battle-tested core.** Wraps [voku/anti-xss](https://github.com/voku/anti-xss), an actively maintained library with an extensive list of evil tags, attributes, and JS patterns.
- **Strict by default, flexible when needed.** Sensible config out of the box; extend or shrink the evil tag/attribute lists per project.
- **Multiple enforcement points.** Reject (validation), clean (middleware), or sanitize on demand (service / Blade) — pick what fits your threat model.

Unlike `strip_tags()` it understands obfuscated payloads (`javascript:` URIs, encoded entities, dangerous attributes). Unlike a full HTML purifier (e.g. HTMLPurifier) it is lighter and Laravel-first.

## Requirements

- PHP **8.3+**
- Laravel **11**, **12**, or **13**

## Installation

```bash
composer require ricventu/laravel-anti-xss
```

The service provider is auto-discovered. Publish the config file with:

```bash
php artisan vendor:publish --tag="anti-xss-config"
```

## Configuration

The published `config/anti-xss.php`:

```php
return [
    'replacement' => '',
    'keep_pre_and_code_tag_content' => false,
    'strip_4byte_chars' => false,
    'evil_attributes' => [
        'add' => [],
        'remove' => [],
    ],
    'evil_html_tags' => [
        'add' => [],
        'remove' => [],
    ],
    'middleware' => [
        'except' => ['password', 'password_confirmation'],
    ],
];
```

| Key | Purpose |
|-----|---------|
| `replacement` | String used in place of stripped malicious content. |
| `keep_pre_and_code_tag_content` | Preserve content of `<pre>` and `<code>` tags. |
| `strip_4byte_chars` | Strip 4-byte UTF-8 characters (e.g. emoji) — useful with non-`utf8mb4` databases. |
| `evil_attributes.add` / `.remove` | Extend or shrink the default list of evil attributes. |
| `evil_html_tags.add` / `.remove` | Extend or shrink the default list of evil tags. |
| `middleware.except` | Field names ignored by `CleanXssInput` middleware. |

## Usage

### Service / Dependency Injection

```php
use Ricventu\LaravelAntiXss\AntiXss;

class CommentController
{
    public function __construct(private AntiXss $antiXss) {}

    public function store(Request $request)
    {
        $body = $this->antiXss->clean($request->input('body'));
        // ...
    }
}
```

### Facade

```php
use Ricventu\LaravelAntiXss\Facades\AntiXss;

$safe = AntiXss::clean($userInput);

if (AntiXss::isXssFound()) {
    logger()->warning('XSS attempt detected.');
}
```

### Helper

```php
$safe = anti_xss($userInput);          // sanitize directly
$service = anti_xss();                  // get the service
$service->setReplacement('[REDACTED]'); // tweak at runtime
```

### Validation rule

Object syntax (recommended):

```php
use Ricventu\LaravelAntiXss\Rules\CleanXss;

$request->validate([
    'bio' => ['required', 'string', new CleanXss],
]);
```

String syntax also works:

```php
$request->validate([
    'bio' => 'required|string|clean_xss',
]);
```

The rule **rejects** input that contains XSS rather than silently mutating it. If you prefer to clean instead, use the middleware below or call `AntiXss::clean()` in your `prepareForValidation()`.

### Middleware

`CleanXssInput` mirrors Laravel's built-in `TrimStrings` middleware: it walks the request payload and sanitizes every string value (excluding the keys listed in `anti-xss.middleware.except`).

It is **opt-in**. Register it in your application bootstrap.

Laravel 11+ (`bootstrap/app.php`):

```php
use Ricventu\LaravelAntiXss\Http\Middleware\CleanXssInput;

->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        CleanXssInput::class,
    ]);
})
```

Laravel 10 (`app/Http/Kernel.php`):

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Ricventu\LaravelAntiXss\Http\Middleware\CleanXssInput::class,
    ],
];
```

### Blade directive

```blade
<p>@xss($comment->body)</p>
```

`@xss($value)` is equivalent to `{{ AntiXss::clean($value) }}` — it sanitizes **and** escapes the result with `e()`.

### Advanced — direct access to the underlying engine

```php
AntiXss::engine()
    ->addNeverAllowedRegex(['/very-custom-pattern/i'])
    ->addNaughtyJavascriptPatterns(['my-tracker(']);
```

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Contributing

Pull requests are welcome. For larger changes, please open an issue first to discuss the direction.

Run the test suite with `composer test` and the static analysis with `composer analyse` before submitting.

## Security

If you discover a security vulnerability, please email **ricventu@gmail.com** instead of opening a public issue. All reports will be reviewed and addressed promptly.

## Credits

- Built on top of [voku/anti-xss](https://github.com/voku/anti-xss) by Lars Moelleken.
- [Riccardo Venturini](https://github.com/Ricventu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).

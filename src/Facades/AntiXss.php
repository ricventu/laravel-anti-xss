<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|array clean(string|array $input)
 * @method static bool|null isXssFound()
 * @method static bool contains(string $input)
 * @method static \voku\helper\AntiXSS engine()
 * @method static \Ricventu\LaravelAntiXss\AntiXss setReplacement(string $replacement)
 * @method static \Ricventu\LaravelAntiXss\AntiXss setKeepPreAndCodeTagContent(bool $bool)
 * @method static \Ricventu\LaravelAntiXss\AntiXss setStripe4byteChars(bool $bool)
 * @method static \Ricventu\LaravelAntiXss\AntiXss addEvilAttributes(array $attrs)
 * @method static \Ricventu\LaravelAntiXss\AntiXss removeEvilAttributes(array $attrs)
 * @method static \Ricventu\LaravelAntiXss\AntiXss addEvilHtmlTags(array $tags)
 * @method static \Ricventu\LaravelAntiXss\AntiXss removeEvilHtmlTags(array $tags)
 *
 * @see \Ricventu\LaravelAntiXss\AntiXss
 */
class AntiXss extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'anti-xss';
    }
}

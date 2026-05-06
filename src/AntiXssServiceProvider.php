<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Ricventu\LaravelAntiXss\Validation\CleanXssValidator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AntiXssServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-anti-xss')
            ->hasConfigFile('anti-xss');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('anti-xss', function ($app) {
            return new AntiXss((array) $app['config']->get('anti-xss', []));
        });

        $this->app->alias('anti-xss', AntiXss::class);
    }

    public function packageBooted(): void
    {
        Blade::directive('xss', function (string $expression): string {
            return "<?php echo e(app('anti-xss')->clean({$expression})); ?>";
        });

        Validator::extend(
            'clean_xss',
            CleanXssValidator::class.'@validate',
            'The :attribute field contains potentially malicious content.'
        );
    }
}

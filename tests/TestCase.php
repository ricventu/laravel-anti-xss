<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ricventu\LaravelAntiXss\AntiXssServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AntiXssServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

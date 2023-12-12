<?php

namespace ChrisWare\PulseHealthCheck\Tests;

use ChrisWare\PulseHealthCheck\PulseHealthCheckServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PulseHealthCheckServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}

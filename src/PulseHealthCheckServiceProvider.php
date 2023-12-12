<?php

namespace ChrisWare\PulseHealthCheck;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PulseHealthCheckServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('pulse-health-check');
    }
}

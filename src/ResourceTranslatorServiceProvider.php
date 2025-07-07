<?php

namespace RobertoPorceddu\ResourceTranslator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ResourceTranslatorServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the Resource Translator package.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('resource-translator');
    }
}

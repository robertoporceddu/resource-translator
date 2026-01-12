<?php

use Spatie\LaravelPackageTools\PackageServiceProvider;
use RobertoPorceddu\ResourceTranslator\ResourceTranslatorServiceProvider;

describe('ResourceTranslatorServiceProvider', function () {
    it('extends PackageServiceProvider', function () {
        $provider = app()->getProvider(ResourceTranslatorServiceProvider::class);

        expect($provider)->toBeInstanceOf(PackageServiceProvider::class);
    });

    it('is registered in the application', function () {
        $provider = app()->getProvider(ResourceTranslatorServiceProvider::class);

        expect($provider)->not->toBeNull();
    });

    it('service provider class exists', function () {
        expect(class_exists(ResourceTranslatorServiceProvider::class))->toBeTrue();
    });
});

